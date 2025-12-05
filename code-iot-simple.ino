#include <WiFi.h>
#include <HTTPClient.h>

// WiFi Configuration
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server Configuration
const char* serverBaseUrl = "http://your-domain.com";
const char* deviceUuid = "YOUR_DEVICE_UUID";

// Sensor Pins
const int VIBRATION_PIN = 34;
const int STATUS_LED = 2;

void setup() {
  Serial.begin(115200);
  pinMode(VIBRATION_PIN, INPUT);
  pinMode(STATUS_LED, OUTPUT);

  connectToWiFi();

  Serial.println("Earthquake Monitor Device Ready");
  Serial.println("Device UUID: " + String(deviceUuid));
}

void loop() {
  // Read vibration sensor
  int vibration = analogRead(VIBRATION_PIN);

  // Send data if vibration detected or every 30 seconds
  static unsigned long lastSend = 0;
  if (vibration > 100 || millis() - lastSend > 30000) {
    sendData(vibration);
    lastSend = millis();
  }

  delay(1000);
}

void connectToWiFi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    digitalWrite(STATUS_LED, !digitalRead(STATUS_LED));
  }

  Serial.println("\nConnected! IP: " + WiFi.localIP().toString());
  digitalWrite(STATUS_LED, HIGH);
}

void sendData(int vibration) {
  if (WiFi.status() != WL_CONNECTED) {
    connectToWiFi();
    return;
  }

  HTTPClient http;
  String url = String(serverBaseUrl) + "/api/v1/devices/" + deviceUuid + "/data";

  http.begin(url);
  http.addHeader("Content-Type", "application/json");

  String payload = "{\"vibration\":" + String(vibration) +
                   ",\"status\":\"online\"," +
                   "\"timestamp\":\"" + getTime() + "\"}";

  Serial.println("Sending: " + payload);

  int httpCode = http.POST(payload);

  if (httpCode == HTTP_CODE_OK) {
    String response = http.getString();
    Serial.println("Response: " + response);

    // Parse response for alerts
    if (response.indexOf("\"alert\":true") > 0) {
      triggerAlert();
    }
  } else {
    Serial.println("HTTP Error: " + String(httpCode));
  }

  http.end();
}

String getTime() {
  // Simple timestamp
  // In production, use NTP or RTC
  return "2024-01-01T12:00:00Z";
}

void triggerAlert() {
  // Alert pattern
  for (int i = 0; i < 5; i++) {
    digitalWrite(STATUS_LED, LOW);
    delay(100);
    digitalWrite(STATUS_LED, HIGH);
    delay(100);
  }
}
