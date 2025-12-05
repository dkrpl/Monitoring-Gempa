#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// WiFi Configuration
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server Configuration
const char* serverUrl = "http://your-domain.com";
const char* deviceUuid = "YOUR_DEVICE_UUID";

// Sensor Configuration
const int SW420_PIN = 34;
const int LED_PIN = 2;
const int BUZZER_PIN = 25;
const int BUTTON_PIN = 0; // For manual test

// Detection Configuration
const int DETECTION_THRESHOLD = 200; // Vibration threshold for earthquake
const float MAGNITUDE_THRESHOLD = 3.0; // Minimum magnitude for earthquake
const unsigned long DETECTION_COOLDOWN = 10000; // 10 seconds between detections
const int SAMPLE_WINDOW = 50; // Sample window in ms
const int NUM_SAMPLES = 10; // Number of samples for averaging

// State variables
unsigned long lastDetectionTime = 0;
bool earthquakeDetected = false;
float currentMagnitude = 0.0;
int samples[NUM_SAMPLES];
int sampleIndex = 0;

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

  // Initialize samples array
  for(int i = 0; i < NUM_SAMPLES; i++) {
    samples[i] = 0;
  }

  Serial.println("Earthquake Detection System Ready");
  Serial.println("Detection Threshold: Magnitude >= " + String(MAGNITUDE_THRESHOLD));
  Serial.println("========================================");
}

void loop() {
  // Maintain WiFi connection
  if (WiFi.status() != WL_CONNECTED) {
    connectToWiFi();
  }

  // Read vibration sensor with averaging
  int vibration = readVibration();

  // Calculate magnitude
  float magnitude = vibrationToMagnitude(vibration);
  currentMagnitude = magnitude;

  // Check for earthquake detection
  unsigned long currentTime = millis();

  if (magnitude >= MAGNITUDE_THRESHOLD) {
    if (!earthquakeDetected && (currentTime - lastDetectionTime >= DETECTION_COOLDOWN)) {
      earthquakeDetected = true;
      lastDetectionTime = currentTime;

      // Send earthquake data
      sendEarthquakeData(vibration, magnitude);

      // Trigger local alert
      triggerEarthquakeAlert(magnitude);

      Serial.println("EARTHQUAKE DETECTED!");
      Serial.print("Magnitude: ");
      Serial.println(magnitude);
      Serial.print("Vibration: ");
      Serial.println(vibration);
    }
  } else {
    earthquakeDetected = false;

    // Send regular data every 30 seconds
    static unsigned long lastRegularSend = 0;
    if (currentTime - lastRegularSend >= 30000) {
      sendRegularData(vibration, magnitude);
      lastRegularSend = currentTime;
    }
  }

  // Manual test button
  if (digitalRead(BUTTON_PIN) == LOW) {
    delay(50); // Debounce
    if (digitalRead(BUTTON_PIN) == LOW) {
      manualTest();
      while(digitalRead(BUTTON_PIN) == LOW); // Wait for release
    }
  }

  // Update status LED
  updateStatusLED(magnitude);

  delay(100);
}

int readVibration() {
  // Read multiple samples and average
  long sum = 0;
  for(int i = 0; i < NUM_SAMPLES; i++) {
    sum += analogRead(SW420_PIN);
    delay(SAMPLE_WINDOW / NUM_SAMPLES);
  }

  int average = sum / NUM_SAMPLES;

  // Store in samples array
  samples[sampleIndex] = average;
  sampleIndex = (sampleIndex + 1) % NUM_SAMPLES;

  return average;
}

float vibrationToMagnitude(int vibration) {
  // Convert vibration (0-1023) to magnitude (0-10)
  if (vibration < 50) return 0;
  if (vibration < 200) return vibration / 200.0 * 2.0;
  if (vibration < 500) return 2.0 + (vibration - 200) / 300.0 * 3.0;
  return 5.0 + (vibration - 500) / 523.0 * 5.0;
}

void sendRegularData(int vibration, float magnitude) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(serverUrl) + "/api/v1/devices/" + deviceUuid + "/data";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<256> doc;
  doc["vibration"] = vibration;
  doc["status"] = "online";
  doc["magnitude_calculated"] = magnitude;
  doc["detection_mode"] = "monitoring";
  doc["timestamp"] = getTimestamp();

  String requestBody;
  serializeJson(doc, requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode == HTTP_CODE_OK) {
    String response = http.getString();
    Serial.println("Regular data sent: Magnitude=" + String(magnitude, 2));
  }

  http.end();
}

void sendEarthquakeData(int vibration, float magnitude) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(serverUrl) + "/api/v1/devices/" + deviceUuid + "/data";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<512> doc;
  doc["vibration"] = vibration;
  doc["status"] = "online";
  doc["magnitude_calculated"] = magnitude;
  doc["detection_mode"] = "earthquake";
  doc["alert_level"] = magnitude >= 5.0 ? "danger" : "warning";
  doc["timestamp"] = getTimestamp();
  doc["battery"] = 100; // Simulate battery

  String requestBody;
  serializeJson(doc, requestBody);

  Serial.println("Sending earthquake data: " + requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode == HTTP_CODE_OK) {
    String response = http.getString();
    DynamicJsonDocument responseDoc(1024);
    deserializeJson(responseDoc, response);

    if (responseDoc["success"]) {
      Serial.println("Earthquake data sent successfully!");

      // Check for alert response
      if (responseDoc["data"]["earthquake_event"]["alert"]) {
        Serial.println("Alert confirmed by server!");
      }
    }
  } else {
    Serial.println("Failed to send earthquake data. HTTP: " + String(httpCode));
  }

  http.end();
}

void triggerEarthquakeAlert(float magnitude) {
  Serial.println("TRIGGERING LOCAL ALERT - Magnitude: " + String(magnitude, 2));

  if (magnitude >= 5.0) {
    // Danger alert - rapid beeping
    for(int i = 0; i < 15; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 1500, 100);
      delay(100);
      digitalWrite(LED_PIN, LOW);
      delay(100);
    }
  } else {
    // Warning alert - slower beeping
    for(int i = 0; i < 8; i++) {
      digitalWrite(LED_PIN, HIGH);
      tone(BUZZER_PIN, 1000, 200);
      delay(300);
      digitalWrite(LED_PIN, LOW);
      delay(300);
    }
  }
}

void updateStatusLED(float magnitude) {
  if (magnitude >= MAGNITUDE_THRESHOLD) {
    // Blink rapidly during earthquake
    digitalWrite(LED_PIN, !digitalRead(LED_PIN));
  } else if (magnitude >= 1.0) {
    // Slow blink for minor activity
    static unsigned long lastBlink = 0;
    if (millis() - lastBlink >= 1000) {
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
      lastBlink = millis();
    }
  } else {
    // Solid for normal operation
    digitalWrite(LED_PIN, HIGH);
  }
}

void manualTest() {
  Serial.println("Manual test triggered");

  // Send test detection to server
  sendTestDetection(4.5); // Test with 4.5 magnitude

  // Visual feedback
  for(int i = 0; i < 3; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 800, 200);
    delay(200);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }
}

void sendTestDetection(float testMagnitude) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(serverUrl) + "/api/v1/devices/" + deviceUuid + "/test-detection";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<128> doc;
  doc["test_magnitude"] = testMagnitude;

  String requestBody;
  serializeJson(doc, requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode == HTTP_CODE_OK) {
    String response = http.getString();
    Serial.println("Test detection sent: " + response);
  }

  http.end();
}

void startupSequence() {
  for(int i = 0; i < 3; i++) {
    digitalWrite(LED_PIN, HIGH);
    tone(BUZZER_PIN, 1000, 100);
    delay(200);
    digitalWrite(LED_PIN, LOW);
    delay(200);
  }
}

void connectToWiFi() {
  Serial.print("Connecting to WiFi");
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
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());

    // Blink 3 times for success
    for(int i = 0; i < 3; i++) {
      digitalWrite(LED_PIN, HIGH);
      delay(100);
      digitalWrite(LED_PIN, LOW);
      delay(100);
    }
  } else {
    Serial.println("\nWiFi connection failed!");
  }
}

String getTimestamp() {
  // Generate simple timestamp
  // In production, use NTP or RTC
  return "2024-01-01T00:00:00Z";
}
