#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

// WiFi Configuration
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server Configuration - SESUAIKAN DENGAN SERVER ANDA
const char* serverUrl = "http://localhost:8000"; // Ganti dengan URL server Laravel Anda
const char* deviceUuid = "YOUR_DEVICE_UUID_HERE"; // Ganti dengan UUID device dari database

// Sensor Configuration
const int SW420_PIN = 34; // Pin untuk sensor SW-420
const int LED_PIN = 2;    // LED onboard ESP32
const int BUZZER_PIN = 25; // Pin untuk buzzer
const int BUTTON_PIN = 0;  // Button untuk manual test

// Detection Configuration
const int VIBRATION_THRESHOLD = 100;  // Threshold untuk deteksi getaran
const float MAGNITUDE_THRESHOLD = 1.0; // Minimal magnitude untuk dikirim
const unsigned long DETECTION_COOLDOWN = 30000; // 30 detik antara deteksi
const int SAMPLE_WINDOW = 100; // Window sampling dalam ms
const int NUM_SAMPLES = 20;    // Jumlah sample untuk averaging

// State variables
unsigned long lastDetectionTime = 0;
bool earthquakeDetected = false;
float currentMagnitude = 0.0;
int vibrationSamples[NUM_SAMPLES];
int sampleIndex = 0;

// Untuk periodic data sending
unsigned long lastRegularSend = 0;
const unsigned long REGULAR_INTERVAL = 60000; // Kirim data setiap 60 detik

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

  Serial.println("\n========================================");
  Serial.println("Earthquake Monitoring System - ESP32");
  Serial.println("Sensor: SW-420 Vibration Sensor");
  Serial.println("Server: " + String(serverUrl));
  Serial.println("Device UUID: " + String(deviceUuid));
  Serial.println("========================================");
}

void loop() {
  // Maintain WiFi connection
  if (WiFi.status() != WL_CONNECTED) {
    reconnectToWiFi();
  }

  // Baca sensor vibration dengan averaging
  int vibration = readVibrationWithAverage();

  // Hitung magnitude dari vibration
  float magnitude = calculateMagnitude(vibration);
  currentMagnitude = magnitude;

  // Cek untuk deteksi gempa
  unsigned long currentTime = millis();

  if (vibration > VIBRATION_THRESHOLD) {
    if (!earthquakeDetected && (currentTime - lastDetectionTime >= DETECTION_COOLDOWN)) {
      earthquakeDetected = true;
      lastDetectionTime = currentTime;

      // Tentukan status berdasarkan magnitude
      String status = (magnitude >= 5.0) ? "danger" : "warning";

      // Kirim data gempa ke server
      if (sendEarthquakeEvent(magnitude, status, vibration)) {
        Serial.println("Earthquake event sent to server!");
        // Trigger local alert
        triggerLocalAlert(magnitude, status);
      }

      Serial.println("EARTHQUAKE DETECTED!");
      Serial.print("Vibration: ");
      Serial.println(vibration);
      Serial.print("Magnitude: ");
      Serial.println(magnitude);
      Serial.print("Status: ");
      Serial.println(status);
    }
  } else {
    earthquakeDetected = false;
  }

  // Kirim regular device log data
  if (currentTime - lastRegularSend >= REGULAR_INTERVAL) {
    sendDeviceLog(vibration, magnitude);
    lastRegularSend = currentTime;
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

  delay(50);
}

// Fungsi untuk membaca vibration dengan averaging
int readVibrationWithAverage() {
  long sum = 0;
  for(int i = 0; i < NUM_SAMPLES; i++) {
    sum += analogRead(SW420_PIN);
    delay(SAMPLE_WINDOW / NUM_SAMPLES);
  }

  int average = sum / NUM_SAMPLES;

  // Simpan dalam array untuk smoothing
  vibrationSamples[sampleIndex] = average;
  sampleIndex = (sampleIndex + 1) % NUM_SAMPLES;

  return average;
}

// Hitung magnitude dari vibration reading
float calculateMagnitude(int vibration) {
  // Konversi vibration (0-4095 untuk ESP32) ke magnitude (0-10)
  // Rumus disederhanakan, bisa disesuaikan dengan kalibrasi sensor
  if (vibration < 100) return 0;
  if (vibration < 500) return (vibration / 500.0) * 3.0;
  if (vibration < 2000) return 3.0 + ((vibration - 500) / 1500.0) * 4.0;
  return 7.0 + ((vibration - 2000) / 2095.0) * 3.0;
}

// Kirim earthquake event ke server Laravel
bool sendEarthquakeEvent(float magnitude, String status, int vibration) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi not connected!");
    return false;
  }

  HTTPClient http;

  // URL untuk create earthquake event
  String url = String(serverUrl) + "/api/v1/earthquake-events";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  // Buat payload JSON sesuai dengan struktur database
  StaticJsonDocument<512> doc;
  doc["device_uuid"] = deviceUuid;
  doc["magnitude"] = magnitude;
  doc["status"] = status;
  doc["occurred_at"] = getCurrentTimestamp();
  doc["vibration_reading"] = vibration;

  // Tambahkan informasi tambahan jika ada
  // doc["latitude"] = 0.0;    // Jika ada GPS
  // doc["longitude"] = 0.0;   // Jika ada GPS
  // doc["depth"] = 10.0;      // Default depth
  doc["description"] = "Automatically detected by SW-420 sensor";

  String requestBody;
  serializeJson(doc, requestBody);

  Serial.println("Sending earthquake event:");
  Serial.println(requestBody);

  int httpCode = http.POST(requestBody);

  bool success = false;

  if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_CREATED) {
    String response = http.getString();
    Serial.println("Response: " + response);

    // Parse response
    DynamicJsonDocument responseDoc(1024);
    DeserializationError error = deserializeJson(responseDoc, response);

    if (!error && responseDoc["success"]) {
      Serial.println("✓ Earthquake event created successfully!");
      success = true;
    }
  } else {
    Serial.println("✗ Error sending earthquake event. HTTP Code: " + String(httpCode));
    if (httpCode == HTTP_CODE_NOT_FOUND) {
      Serial.println("Endpoint not found. Check server URL.");
    }
  }

  http.end();
  return success;
}

// Kirim device log reguler
void sendDeviceLog(int vibration, float magnitude) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;

  // URL untuk device logs
  String url = String(serverUrl) + "/api/v1/device-logs";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  StaticJsonDocument<256> doc;
  doc["device_uuid"] = deviceUuid;
  doc["status"] = "online";
  doc["magnitude"] = magnitude;
  doc["vibration"] = vibration;
  doc["logged_at"] = getCurrentTimestamp();

  String requestBody;
  serializeJson(doc, requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_CREATED) {
    Serial.println("Device log sent: Magnitude=" + String(magnitude, 2));
  } else {
    Serial.println("Failed to send device log. HTTP: " + String(httpCode));
  }

  http.end();
}

// Trigger alert lokal
void triggerLocalAlert(float magnitude, String status) {
  Serial.println("Local alert triggered!");

  if (status == "danger") {
    // Danger alert - pola cepat
    for(int i = 0; i < 10; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 1500, 100);
      delay(150);
      digitalWrite(LED_PIN, LOW);
      delay(100);
    }
  } else {
    // Warning alert - pola sedang
    for(int i = 0; i < 6; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 1000, 200);
      delay(300);
      digitalWrite(LED_PIN, LOW);
      delay(300);
    }
  }
}

// Update status LED berdasarkan magnitude
void updateStatusLED(float magnitude) {
  static unsigned long lastBlink = 0;
  unsigned long currentTime = millis();

  if (magnitude >= 5.0) {
    // Blink cepat untuk danger
    if (currentTime - lastBlink >= 200) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else if (magnitude >= 3.0) {
    // Blink sedang untuk warning
    if (currentTime - lastBlink >= 500) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else if (magnitude >= 1.0) {
    // Blink lambat untuk minor activity
    if (currentTime - lastBlink >= 1000) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = currentTime;
    }
  } else {
    // Normal operation - LED hidup
    digitalWrite(LED_PIN, HIGH);
  }
}

// Manual test function
void manualTest() {
  Serial.println("Manual test triggered");

  // Send test earthquake event
  sendTestEvent(4.2, "warning");

  // Visual feedback
  for(int i = 0; i < 3; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 1200, 300);
    delay(400);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }
}

// Send test earthquake event
bool sendTestEvent(float magnitude, String status) {
  if (WiFi.status() != WL_CONNECTED) return false;

  HTTPClient http;
  String url = String(serverUrl) + "/api/v1/test-earthquake";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<256> doc;
  doc["device_uuid"] = deviceUuid;
  doc["magnitude"] = magnitude;
  doc["status"] = status;
  doc["is_test"] = true;
  doc["occurred_at"] = getCurrentTimestamp();

  String requestBody;
  serializeJson(doc, requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode == HTTP_CODE_OK) {
    String response = http.getString();
    Serial.println("Test event sent: " + response);
    return true;
  }

  http.end();
  return false;
}

// Startup sequence
void startupSequence() {
  for(int i = 0; i < 3; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 1000, 100);
    delay(200);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }
  delay(500);

  // Special pattern for earthquake monitoring
  for(int i = 0; i < 2; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 800, 300);
    delay(400);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }
}

// Connect to WiFi
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
    Serial.println("\nWiFi connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    // Blink pattern for success
    for(int i = 0; i < 3; i++) {
      digitalWrite(LED_PIN, HIGH);
      delay(100);
      digitalWrite(LED_PIN, LOW);
      delay(100);
    }
    digitalWrite(LED_PIN, HIGH); // Keep LED on

    // Wait for time sync
    Serial.print("Waiting for time sync");
    for(int i = 0; i < 10; i++) {
      delay(1000);
      Serial.print(".");
    }
    Serial.println();

  } else {
    Serial.println("\nWiFi connection failed!");
    digitalWrite(LED_PIN, LOW);
  }
}

// Reconnect to WiFi
void reconnectToWiFi() {
  static unsigned long lastReconnectAttempt = 0;
  unsigned long currentTime = millis();

  if (currentTime - lastReconnectAttempt >= 10000) { // Coba reconnect setiap 10 detik
    lastReconnectAttempt = currentTime;
    Serial.println("Attempting WiFi reconnection...");
    WiFi.disconnect();
    WiFi.reconnect();

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("WiFi reconnected!");
      digitalWrite(LED_PIN, HIGH);
    }
  }
}

// Get current timestamp in ISO 8601 format
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

// Fungsi tambahan untuk debugging
void printSensorData() {
  Serial.print("Vibration: ");
  Serial.print(analogRead(SW420_PIN));
  Serial.print(" | Magnitude: ");
  Serial.print(currentMagnitude, 2);
  Serial.print(" | WiFi: ");
  Serial.println(WiFi.status() == WL_CONNECTED ? "Connected" : "Disconnected");
}
