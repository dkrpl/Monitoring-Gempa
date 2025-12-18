#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>
#include <time.h>

// WiFi Configuration
const char* ssid = "DT";
const char* password = "joinajaaa1";

// Server Configuration - HTTPS
const char* serverUrl = "https://zaxlab.my.id"; // Ganti dengan domain HTTPS Anda
const char* deviceUuid = "286d6adc-f99c-48c4-b045-1ec57c164e7c";

// Untuk HTTPS, tambahkan fingerprint SSL jika perlu
// const char* sslFingerprint = "AA BB CC DD EE FF 00 11 22 33 44 55 66 77 88 99 AA BB CC DD";

// Sensor Configuration
const int SW420_PIN = D0;        // Pin analog untuk sensor SW-420
const int LED_PIN = 2;           // LED onboard ESP8266 (D4)
const int BUZZER_PIN = D1;       // Pin untuk buzzer
const int BUTTON_PIN = D3;       // Button untuk manual test

// Detection Configuration
const int VIBRATION_THRESHOLD = 50;
const float MAGNITUDE_THRESHOLD = 3.0;
const unsigned long DETECTION_COOLDOWN = 30000;
const int SAMPLE_WINDOW = 100;
const int NUM_SAMPLES = 20;

// State variables
unsigned long lastDetectionTime = 0;
bool earthquakeDetected = false;
float currentMagnitude = 0.0;
int vibrationSamples[NUM_SAMPLES];
int sampleIndex = 0;

// Buffer untuk data
const int DATA_BUFFER_SIZE = 5;
struct EarthquakeData {
    int vibration;
    float magnitude;
    String timestamp;
    bool shouldSend;
};
EarthquakeData dataBuffer[DATA_BUFFER_SIZE];
int bufferIndex = 0;

// Heartbeat
unsigned long lastHeartbeat = 0;
const unsigned long HEARTBEAT_INTERVAL = 60000;

void setup() {
    Serial.begin(115200);

    // Initialize pins
    pinMode(SW420_PIN, INPUT);
    pinMode(LED_PIN, OUTPUT);
    pinMode(BUZZER_PIN, OUTPUT);
    pinMode(BUTTON_PIN, INPUT_PULLUP);

    startupSequence();
    connectToWiFi();

    // Initialize NTP
    configTime(0, 0, "pool.ntp.org");

    // Initialize arrays
    for(int i = 0; i < NUM_SAMPLES; i++) {
        vibrationSamples[i] = 0;
    }

    for(int i = 0; i < DATA_BUFFER_SIZE; i++) {
        dataBuffer[i].vibration = 0;
        dataBuffer[i].magnitude = 0.0;
        dataBuffer[i].timestamp = "";
        dataBuffer[i].shouldSend = false;
    }

    Serial.println("\n========================================");
    Serial.println("Earthquake Monitoring System - ESP8266");
    Serial.println("HTTPS Mode");
    Serial.println("========================================");
    Serial.println("Server: " + String(serverUrl));
    Serial.println("Device UUID: " + String(deviceUuid));
    Serial.println("========================================");
}

void loop() {
    if (WiFi.status() != WL_CONNECTED) {
        reconnectToWiFi();
        return;
    }

    int vibration = readVibrationWithAverage();
    float magnitude = calculateMagnitude(vibration);
    currentMagnitude = magnitude;

    if (magnitude >= MAGNITUDE_THRESHOLD) {
        unsigned long currentTime = millis();

        if (!earthquakeDetected && (currentTime - lastDetectionTime >= DETECTION_COOLDOWN)) {
            earthquakeDetected = true;
            lastDetectionTime = currentTime;

            saveToBuffer(vibration, magnitude, true);

            Serial.println("\n=== EARTHQUAKE DETECTED ===");
            Serial.print("Vibration: ");
            Serial.println(vibration);
            Serial.print("Magnitude: ");
            Serial.println(magnitude);
            Serial.println("==========================\n");

            triggerLocalAlert(magnitude);
            sendDataToServer(vibration, magnitude);
        }
    } else {
        earthquakeDetected = false;
    }

    // Heartbeat
    unsigned long currentTime = millis();
    if (currentTime - lastHeartbeat >= HEARTBEAT_INTERVAL) {
        sendHeartbeat();
        lastHeartbeat = currentTime;
    }

    // Manual test
    if (digitalRead(BUTTON_PIN) == LOW) {
        delay(50);
        if (digitalRead(BUTTON_PIN) == LOW) {
            manualTest();
            while(digitalRead(BUTTON_PIN) == LOW);
        }
    }

    updateStatusLED(currentMagnitude);
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
    if (vibration < 50) return 0;
    if (vibration < 200) return (vibration / 200.0) * 2.0;
    if (vibration < 500) return 2.0 + ((vibration - 200) / 300.0) * 3.0;
    return 5.0 + ((vibration - 500) / 523.0) * 5.0;
}

// ==================== HTTPS FUNCTIONS ====================
bool sendDataToServer(int vibration, float magnitude) {
    // Gunakan WiFiClientSecure untuk HTTPS
    WiFiClientSecure client;
    HTTPClient http;

    // Nonaktifkan SSL verification jika self-signed certificate
    client.setInsecure(); // HATI-HATI: Ini bypass SSL verification

    // Atau gunakan fingerprint jika ada
    // client.setFingerprint(sslFingerprint);

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/data";

    Serial.println("HTTPS: Sending earthquake data...");
    Serial.println("URL: " + url);

    // Mulai koneksi HTTPS
    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    // Tambahkan timeout
    http.setTimeout(10000);
    http.setFollowRedirects(HTTPC_STRICT_FOLLOW_REDIRECTS);

    // Buat payload
    StaticJsonDocument<256> doc;
    doc["vibration"] = vibration;
    doc["status"] = "online";
    doc["timestamp"] = getCurrentTimestamp();
    doc["battery"] = 95;
    doc["temperature"] = 27.5;
    doc["humidity"] = 65.0;

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.println("Request: " + requestBody);

    int httpCode = http.POST(requestBody);

    bool success = false;

    if (httpCode > 0) {
        String response = http.getString();
        Serial.println("Response Code: " + String(httpCode));
        Serial.println("Response: " + response);

        DynamicJsonDocument responseDoc(1024);
        DeserializationError error = deserializeJson(responseDoc, response);

        if (!error) {
            if (responseDoc["success"]) {
                success = true;
                Serial.println("✓ HTTPS Data sent successfully!");

                if (responseDoc["data"]["earthquake_event"]) {
                    Serial.println("Earthquake Event Created!");
                }
            } else {
                Serial.print("Server error: ");
                Serial.println(responseDoc["message"].as<String>());
            }
        }
    } else {
        Serial.print("HTTPS Error: ");
        Serial.println(http.errorToString(httpCode).c_str());

        // Debug info
        Serial.print("WiFi Status: ");
        Serial.println(WiFi.status());
        Serial.print("Server: ");
        Serial.println(serverUrl);
    }

    http.end();
    return success;
}

void sendHeartbeat() {
    WiFiClientSecure client;
    HTTPClient http;

    client.setInsecure(); // Bypass SSL verification

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/heartbeat";

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(5000);

    StaticJsonDocument<128> doc;
    doc["status"] = "online";
    doc["battery"] = 95;
    doc["temperature"] = 27.5;
    doc["humidity"] = 65.0;

    String requestBody;
    serializeJson(doc, requestBody);

    int httpCode = http.POST(requestBody);

    if (httpCode > 0) {
        Serial.println("HTTPS Heartbeat sent (" + String(httpCode) + ")");
    } else {
        Serial.print("HTTPS Heartbeat failed: ");
        Serial.println(http.errorToString(httpCode).c_str());
    }

    http.end();
}

void sendBulkData() {
    if (bufferIndex == 0) return;

    WiFiClientSecure client;
    HTTPClient http;

    client.setInsecure();

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/bulk-upload";

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(10000);

    StaticJsonDocument<1024> doc;
    JsonArray logs = doc.createNestedArray("logs");

    int sendCount = 0;
    for (int i = 0; i < bufferIndex; i++) {
        if (dataBuffer[i].shouldSend) {
            JsonObject log = logs.createNestedObject();
            log["vibration"] = dataBuffer[i].vibration;
            log["status"] = "online";
            log["timestamp"] = dataBuffer[i].timestamp;
            sendCount++;
        }
    }

    if (sendCount == 0) {
        http.end();
        return;
    }

    String requestBody;
    serializeJson(doc, requestBody);

    int httpCode = http.POST(requestBody);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.println("HTTPS Bulk sent (" + String(sendCount) + " records)");
    } else {
        Serial.print("HTTPS Bulk failed: ");
        Serial.println(http.errorToString(httpCode).c_str());
    }

    http.end();
}

void manualTest() {
    Serial.println("\n=== HTTPS MANUAL TEST ===");

    WiFiClientSecure client;
    HTTPClient http;

    client.setInsecure();

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/test-detection";

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(5000);

    StaticJsonDocument<128> doc;
    doc["test_magnitude"] = 4.5;

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.println("HTTPS Test request: " + requestBody);

    int httpCode = http.POST(requestBody);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.println("HTTPS Test response: " + response);
    } else {
        Serial.print("HTTPS Test failed: ");
        Serial.println(http.errorToString(httpCode).c_str());
    }

    http.end();

    // Visual feedback
    for(int i = 0; i < 3; i++) {
        digitalWrite(LED_PIN, HIGH);
        tone(BUZZER_PIN, 1200, 200);
        delay(300);
        digitalWrite(LED_PIN, LOW);
        delay(200);
    }

    Serial.println("=== TEST COMPLETE ===\n");
}

// ==================== HELPER FUNCTIONS ====================
void saveToBuffer(int vibration, float magnitude, bool shouldSend) {
    if (bufferIndex < DATA_BUFFER_SIZE) {
        dataBuffer[bufferIndex].vibration = vibration;
        dataBuffer[bufferIndex].magnitude = magnitude;
        dataBuffer[bufferIndex].timestamp = getCurrentTimestamp();
        dataBuffer[bufferIndex].shouldSend = shouldSend;
        bufferIndex++;
    }

    if (bufferIndex >= DATA_BUFFER_SIZE) {
        processBuffer();
        bufferIndex = 0;
    }
}

void processBuffer() {
    bool hasEarthquakeData = false;

    for (int i = 0; i < DATA_BUFFER_SIZE; i++) {
        if (dataBuffer[i].shouldSend) {
            hasEarthquakeData = true;
            break;
        }
    }

    if (hasEarthquakeData) {
        sendBulkData();
    }

    for (int i = 0; i < DATA_BUFFER_SIZE; i++) {
        dataBuffer[i].vibration = 0;
        dataBuffer[i].magnitude = 0.0;
        dataBuffer[i].timestamp = "";
        dataBuffer[i].shouldSend = false;
    }
}

void triggerLocalAlert(float magnitude) {
    Serial.println("ALERT! Magnitude: " + String(magnitude, 1));

    if (magnitude >= 7.0) {
        for(int i = 0; i < 15; i++) {
            digitalWrite(LED_PIN, HIGH);
            tone(BUZZER_PIN, 2000, 80);
            delay(100);
            digitalWrite(LED_PIN, LOW);
            delay(50);
        }
    } else if (magnitude >= 5.0) {
        for(int i = 0; i < 10; i++) {
            digitalWrite(LED_PIN, HIGH);
            tone(BUZZER_PIN, 1500, 100);
            delay(150);
            digitalWrite(LED_PIN, LOW);
            delay(100);
        }
    } else if (magnitude >= 3.0) {
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
        if (currentTime - lastBlink >= 100) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    } else if (magnitude >= 5.0) {
        if (currentTime - lastBlink >= 200) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    } else if (magnitude >= 3.0) {
        if (currentTime - lastBlink >= 500) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    } else {
        if (currentTime - lastBlink >= 2000) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
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
        Serial.println("\nWiFi connected!");
        Serial.print("IP: ");
        Serial.println(WiFi.localIP());

        for(int i = 0; i < 3; i++) {
            digitalWrite(LED_PIN, HIGH);
            delay(100);
            digitalWrite(LED_PIN, LOW);
            delay(100);
        }
        digitalWrite(LED_PIN, HIGH);
    } else {
        Serial.println("\nWiFi connection failed!");
        digitalWrite(LED_PIN, LOW);
    }
}

void reconnectToWiFi() {
    static unsigned long lastReconnectAttempt = 0;
    unsigned long currentTime = millis();

    if (currentTime - lastReconnectAttempt >= 10000) {
        lastReconnectAttempt = currentTime;
        Serial.println("Reconnecting to WiFi...");
        WiFi.disconnect();
        delay(1000);
        WiFi.begin(ssid, password);
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

void startupSequence() {
    Serial.println("Starting HTTPS Earthquake Monitoring...");

    for(int i = 0; i < 3; i++) {
        digitalWrite(LED_PIN, HIGH);
        tone(BUZZER_PIN, 1000, 100);
        delay(200);
        digitalWrite(LED_PIN, LOW);
        delay(200);
    }
}
