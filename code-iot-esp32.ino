#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

// WiFi Configuration
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server Configuration
const char* serverUrl = "http://YOUR_SERVER_IP:8000"; // Ganti dengan IP server Laravel
const char* deviceUuid = "YOUR_DEVICE_UUID_HERE";     // Ganti dengan UUID device

// Sensor Configuration
const int SW420_PIN = 34;     // Pin untuk sensor SW-420 (ADC1)
const int LED_PIN = 2;        // LED onboard ESP32
const int BUZZER_PIN = 25;    // Pin untuk buzzer
const int BUTTON_PIN = 0;     // Button untuk manual test

// Detection Configuration
const int VIBRATION_THRESHOLD = 50;    // Threshold untuk mendeteksi getaran awal
const float MAGNITUDE_THRESHOLD = 3.0; // Hanya kirim data jika magnitude ≥ 3.0
const unsigned long DETECTION_COOLDOWN = 30000; // 30 detik antara deteksi
const int SAMPLE_WINDOW = 100;         // Window sampling dalam ms
const int NUM_SAMPLES = 20;            // Jumlah sample untuk averaging

// State variables
unsigned long lastDetectionTime = 0;
bool earthquakeDetected = false;
float currentMagnitude = 0.0;
int vibrationSamples[NUM_SAMPLES];
int sampleIndex = 0;

// Buffer untuk data gempa signifikan
const int MAX_BUFFER_SIZE = 5;
struct EarthquakeData {
    int vibration;
    float magnitude;
    String timestamp;
    bool sent;
};
EarthquakeData earthquakeBuffer[MAX_BUFFER_SIZE];
int bufferIndex = 0;

// Heartbeat interval
unsigned long lastHeartbeat = 0;
const unsigned long HEARTBEAT_INTERVAL = 60000; // Heartbeat setiap 60 detik

void setup() {
  Serial.begin(115200);

  // Initialize pins
  pinMode(SW420_PIN, INPUT);
  pinMode(LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(BUTTON_PIN, INPUT_PULLUP);

  // Initial LED sequence
  startupSequence();

  // Connect to WiFi
  connectToWiFi();

  // Initialize NTP untuk timestamp
  configTime(0, 0, "pool.ntp.org");

  // Initialize samples array
  for(int i = 0; i < NUM_SAMPLES; i++) {
    vibrationSamples[i] = 0;
  }

  // Initialize earthquake buffer
  for(int i = 0; i < MAX_BUFFER_SIZE; i++) {
    earthquakeBuffer[i].vibration = 0;
    earthquakeBuffer[i].magnitude = 0.0;
    earthquakeBuffer[i].timestamp = "";
    earthquakeBuffer[i].sent = false;
  }

  Serial.println("\n========================================");
  Serial.println("Earthquake Monitoring System - ESP32");
  Serial.println("Sensor: SW-420 Vibration Sensor");
  Serial.println("Mode: Send data only when magnitude ≥ 3.0");
  Serial.println("Server: " + String(serverUrl));
  Serial.println("Device UUID: " + String(deviceUuid));
  Serial.println("========================================");
}

void loop() {
  // Maintain WiFi connection
  if (WiFi.status() != WL_CONNECTED) {
    reconnectToWiFi();
    return;
  }

  // Baca sensor vibration dengan averaging
  int vibration = readVibrationWithAverage();

  // Hitung magnitude dari vibration
  float magnitude = calculateMagnitude(vibration);
  currentMagnitude = magnitude;

  // Cek untuk deteksi gempa signifikan
  unsigned long currentTime = millis();

  if (vibration > VIBRATION_THRESHOLD) {
    if (!earthquakeDetected && (currentTime - lastDetectionTime >= DETECTION_COOLDOWN)) {
      earthquakeDetected = true;
      lastDetectionTime = currentTime;

      Serial.println("\n=== VIBRATION DETECTED ===");
      Serial.print("Vibration: ");
      Serial.println(vibration);
      Serial.print("Magnitude: ");
      Serial.println(magnitude, 2);

      // Hanya proses jika magnitude ≥ 3.0
      if (magnitude >= MAGNITUDE_THRESHOLD) {
        Serial.println("Significant earthquake detected! (≥ 3.0)");

        // Simpan ke buffer untuk dikirim
        saveToBuffer(vibration, magnitude);

        // Kirim data ke server
        if (sendEarthquakeData(vibration, magnitude)) {
          Serial.println("✓ Data sent to server successfully!");
        } else {
          Serial.println("✗ Failed to send data to server");
        }

        // Trigger local alert berdasarkan magnitude
        triggerLocalAlert(magnitude);
      } else {
        Serial.println("Minor vibration, not sending to server (< 3.0)");
        // Blink LED untuk indikasi minor vibration
        blinkLED(3, 200);
      }
    }
  } else {
    earthquakeDetected = false;
  }

  // Coba kirim data yang belum terkirim di buffer
  sendBufferedData();

  // Kirim heartbeat secara teratur
  if (currentTime - lastHeartbeat >= HEARTBEAT_INTERVAL) {
    sendHeartbeat();
    lastHeartbeat = currentTime;
  }

  // Manual test button
  if (digitalRead(BUTTON_PIN) == LOW) {
    delay(50); // Debounce
    if (digitalRead(BUTTON_PIN) == LOW) {
      manualTest();
      while(digitalRead(BUTTON_PIN) == LOW); // Tunggu sampai dilepas
    }
  }

  // Update status LED
  updateStatusLED(magnitude);

  // Debug print setiap 10 detik
  static unsigned long lastPrint = 0;
  if (currentTime - lastPrint >= 10000) {
    lastPrint = currentTime;
    printSystemStatus();
  }

  delay(100);
}

// ==================== SENSOR FUNCTIONS ====================

int readVibrationWithAverage() {
  long sum = 0;
  for(int i = 0; i < NUM_SAMPLES; i++) {
    sum += analogRead(SW420_PIN);
    delay(SAMPLE_WINDOW / NUM_SAMPLES);
  }
  return sum / NUM_SAMPLES;
}

float calculateMagnitude(int vibration) {
  // Convert vibration (0-1023) to magnitude (0-10 scale)
  // Sesuai dengan fungsi convertToMagnitude di Laravel
  if (vibration < 50) return 0;
  if (vibration < 200) return (vibration / 200.0) * 2.0;
  if (vibration < 500) return 2.0 + ((vibration - 200) / 300.0) * 3.0;
  return 5.0 + ((vibration - 500) / 523.0) * 5.0;
}

// ==================== SERVER COMMUNICATION ====================

bool sendEarthquakeData(int vibration, float magnitude) {
  HTTPClient http;

  String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/data";

  Serial.println("Sending to: " + url);

  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  // Buat payload
  StaticJsonDocument<256> doc;
  doc["vibration"] = vibration;
  doc["status"] = "online";
  doc["timestamp"] = getCurrentTimestamp();
  doc["battery"] = 95; // Simulasi battery level

  String requestBody;
  serializeJson(doc, requestBody);

  Serial.println("Request body: " + requestBody);

  int httpCode = http.POST(requestBody);

  bool success = false;

  if (httpCode > 0) {
    String response = http.getString();
    Serial.print("HTTP Code: ");
    Serial.println(httpCode);

    if (httpCode == 200 || httpCode == 201) {
      // Parse response
      DynamicJsonDocument responseDoc(1024);
      DeserializationError error = deserializeJson(responseDoc, response);

      if (!error && responseDoc["success"]) {
        success = true;

        // Tampilkan info earthquake jika ada
        if (responseDoc["data"]["earthquake_event"]) {
          Serial.println("\n=== EARTHQUAKE EVENT CREATED ===");
          Serial.print("Event ID: ");
          Serial.println(responseDoc["data"]["earthquake_event"]["id"].as<String>());
          Serial.print("Magnitude: ");
          Serial.println(responseDoc["data"]["earthquake_event"]["magnitude"].as<float>());
          Serial.print("Status: ");
          Serial.println(responseDoc["data"]["earthquake_event"]["status"].as<String>());
          Serial.print("Alert: ");
          Serial.println(responseDoc["data"]["earthquake_event"]["alert_type"].as<String>());
          Serial.println("================================");
        }
      }
    } else {
      Serial.println("Server error response: " + response);
    }
  } else {
    Serial.print("HTTP Error: ");
    Serial.println(http.errorToString(httpCode).c_str());
  }

  http.end();
  return success;
}

void sendBufferedData() {
  for (int i = 0; i < bufferIndex; i++) {
    if (!earthquakeBuffer[i].sent) {
      Serial.print("Retrying to send buffered data #");
      Serial.println(i);

      if (sendEarthquakeData(earthquakeBuffer[i].vibration, earthquakeBuffer[i].magnitude)) {
        earthquakeBuffer[i].sent = true;
        Serial.println("Buffered data sent successfully");
      } else {
        Serial.println("Failed to send buffered data, will retry later");
      }
      delay(1000); // Delay antar retry
    }
  }
}

void sendHeartbeat() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;

  String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/heartbeat";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<128> doc;
  doc["status"] = "online";
  doc["battery"] = 95;
  doc["timestamp"] = getCurrentTimestamp();

  String requestBody;
  serializeJson(doc, requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode == 200) {
    Serial.println("✓ Heartbeat sent");
  } else {
    Serial.print("Heartbeat failed. HTTP: ");
    Serial.println(httpCode);
  }

  http.end();
}

void saveToBuffer(int vibration, float magnitude) {
  if (bufferIndex < MAX_BUFFER_SIZE) {
    earthquakeBuffer[bufferIndex].vibration = vibration;
    earthquakeBuffer[bufferIndex].magnitude = magnitude;
    earthquakeBuffer[bufferIndex].timestamp = getCurrentTimestamp();
    earthquakeBuffer[bufferIndex].sent = false;
    bufferIndex++;

    Serial.print("Data saved to buffer. Buffer size: ");
    Serial.println(bufferIndex);
  } else {
    Serial.println("Buffer full! Cannot save more data.");
    // Geser buffer (FIFO)
    for (int i = 0; i < MAX_BUFFER_SIZE - 1; i++) {
      earthquakeBuffer[i] = earthquakeBuffer[i + 1];
    }
    bufferIndex = MAX_BUFFER_SIZE - 1;
    saveToBuffer(vibration, magnitude); // Coba simpan lagi
  }
}

// ==================== LOCAL ALERT FUNCTIONS ====================

void triggerLocalAlert(float magnitude) {
  Serial.print("Local alert triggered! Magnitude: ");
  Serial.println(magnitude, 2);

  if (magnitude >= 7.0) {
    // Critical alert - pola sangat cepat
    for(int i = 0; i < 15; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 2000, 100);
      delay(80);
      digitalWrite(LED_PIN, LOW);
      tone(BUZZER_PIN, 1500, 100);
      delay(80);
    }
  } else if (magnitude >= 5.0) {
    // Danger alert - pola cepat
    for(int i = 0; i < 10; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 1500, 100);
      delay(150);
      digitalWrite(LED_PIN, LOW);
      delay(100);
    }
  } else if (magnitude >= 3.0) {
    // Warning alert - pola sedang
    for(int i = 0; i < 6; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 1000, 200);
      delay(300);
      digitalWrite(LED_PIN, LOW);
      delay(200);
    }
  }
}

void updateStatusLED(float magnitude) {
  static unsigned long lastBlink = 0;
  unsigned long currentTime = millis();

  if (magnitude >= 7.0) {
    // Critical - blink sangat cepat
    if (currentTime - lastBlink >= 80) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else if (magnitude >= 5.0) {
    // Danger - blink cepat
    if (currentTime - lastBlink >= 150) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else if (magnitude >= 3.0) {
    // Warning - blink sedang
    if (currentTime - lastBlink >= 500) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else if (magnitude >= 1.0) {
    // Minor - blink lambat
    if (currentTime - lastBlink >= 1000) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else {
    // Normal - LED hidup
    digitalWrite(LED_PIN, HIGH);
  }
}

void blinkLED(int count, int delayTime) {
  for(int i = 0; i < count; i++) {
    digitalWrite(LED_PIN, HIGH);
    delay(delayTime);
    digitalWrite(LED_PIN, LOW);
    delay(delayTime);
  }
}

// ==================== MANUAL TEST ====================

void manualTest() {
  Serial.println("\n=== MANUAL TEST TRIGGERED ===");

  // Test dengan magnitude berbeda
  float testMagnitudes[] = {2.5, 4.0, 6.0, 8.0};

  for (int i = 0; i < 4; i++) {
    Serial.print("\nTesting magnitude: ");
    Serial.println(testMagnitudes[i]);

    if (testMagnitudes[i] >= MAGNITUDE_THRESHOLD) {
      Serial.println("This would trigger server notification");

      // Simulasi vibration untuk magnitude ini
      int simulatedVibration = magnitudeToVibration(testMagnitudes[i]);

      // Kirim test data
      HTTPClient http;
      String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/test-detection";

      http.begin(url);
      http.addHeader("Content-Type", "application/json");

      StaticJsonDocument<128> doc;
      doc["test_magnitude"] = testMagnitudes[i];

      String requestBody;
      serializeJson(doc, requestBody);

      Serial.println("Sending test: " + requestBody);

      int httpCode = http.POST(requestBody);

      if (httpCode == 200) {
        String response = http.getString();
        Serial.println("Test response: " + response);
      }

      http.end();
    } else {
      Serial.println("Below threshold (3.0), not sending");
    }

    // Visual feedback
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 1200, 200);
    delay(500);
    digitalWrite(LED_PIN, LOW);
    delay(500);
  }

  Serial.println("=== MANUAL TEST COMPLETED ===");
}

int magnitudeToVibration(float magnitude) {
  // Inverse dari calculateMagnitude
  if (magnitude <= 0) return 0;
  if (magnitude <= 2) return (magnitude / 2.0) * 200;
  if (magnitude <= 5) return 200 + ((magnitude - 2.0) / 3.0) * 300;
  return 500 + ((magnitude - 5.0) / 5.0) * 523;
}

// ==================== UTILITY FUNCTIONS ====================

void startupSequence() {
  Serial.println("Starting Earthquake Monitoring Device...");

  for(int i = 0; i < 3; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 1000, 100);
    delay(200);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }

  // Pattern khusus untuk earthquake monitoring
  for(int i = 0; i < 2; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 800, 300);
    delay(400);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }
}

void connectToWiFi() {
  Serial.print("Connecting to WiFi: ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    digitalWrite(LED_PIN, !digitalRead(LED_PIN));
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✓ WiFi connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    // Blink pattern for success
    for(int i = 0; i < 3; i++) {
      digitalWrite(LED_PIN, HIGH);
      delay(100);
      digitalWrite(LED_PIN, LOW);
      delay(100);
    }
    digitalWrite(LED_PIN, HIGH);

  } else {
    Serial.println("\n✗ WiFi connection failed!");
    digitalWrite(LED_PIN, LOW);
  }
}

void reconnectToWiFi() {
  static unsigned long lastReconnectAttempt = 0;
  unsigned long currentTime = millis();

  if (currentTime - lastReconnectAttempt >= 10000) {
    lastReconnectAttempt = currentTime;
    Serial.println("Attempting WiFi reconnection...");
    WiFi.disconnect();
    delay(1000);
    WiFi.begin(ssid, password);

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("✓ WiFi reconnected!");
      digitalWrite(LED_PIN, HIGH);
    }
  }
}

String getCurrentTimestamp() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Failed to obtain time");
    return "2024-01-01T00:00:00Z";
  }

  char buffer[25];
  strftime(buffer, sizeof(buffer), "%Y-%m-%dT%H:%M:%SZ", &timeinfo);
  return String(buffer);
}

void printSystemStatus() {
  Serial.print("\n[System Status] ");
  Serial.print("Vibration: ");
  Serial.print(analogRead(SW420_PIN));
  Serial.print(" | Mag: ");
  Serial.print(currentMagnitude, 2);
  Serial.print(" | Buffer: ");
  Serial.print(bufferIndex);
  Serial.print("/");
  Serial.print(MAX_BUFFER_SIZE);
  Serial.print(" | WiFi: ");
  Serial.println(WiFi.status() == WL_CONNECTED ? "Connected" : "Disconnected");
}
