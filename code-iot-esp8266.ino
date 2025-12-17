#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

// WiFi Configuration
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server Configuration
const char* serverUrl = "http://YOUR_SERVER_IP:8000"; // Ganti dengan IP server Laravel
const char* deviceUuid = "YOUR_DEVICE_UUID"; // UUID dari database Laravel

// Sensor Configuration
const int SW420_PIN = A0;        // Pin analog untuk sensor SW-420 di ESP8266
const int LED_PIN = 2;           // LED onboard ESP8266 (D4)
const int BUZZER_PIN = D1;       // Pin untuk buzzer
const int BUTTON_PIN = D3;       // Button untuk manual test

// Detection Configuration
const int VIBRATION_THRESHOLD = 50;    // Threshold awal untuk deteksi getaran
const float MAGNITUDE_THRESHOLD = 3.0; // Hanya kirim jika magnitude >= 3.0
const unsigned long DETECTION_COOLDOWN = 30000; // 30 detik antara pengiriman
const int SAMPLE_WINDOW = 100;         // Window sampling dalam ms
const int NUM_SAMPLES = 20;            // Jumlah sample untuk averaging

// State variables
unsigned long lastDetectionTime = 0;
bool earthquakeDetected = false;
float currentMagnitude = 0.0;
int vibrationSamples[NUM_SAMPLES];
int sampleIndex = 0;

// Buffer untuk menyimpan data sebelum dikirim
const int DATA_BUFFER_SIZE = 5;
struct EarthquakeData {
    int vibration;
    float magnitude;
    String timestamp;
    bool shouldSend; // true jika magnitude >= 3.0
};
EarthquakeData dataBuffer[DATA_BUFFER_SIZE];
int bufferIndex = 0;

// Untuk heartbeat
unsigned long lastHeartbeat = 0;
const unsigned long HEARTBEAT_INTERVAL = 60000; // 60 detik

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

    // Initialize buffer
    for(int i = 0; i < DATA_BUFFER_SIZE; i++) {
        dataBuffer[i].vibration = 0;
        dataBuffer[i].magnitude = 0.0;
        dataBuffer[i].timestamp = "";
        dataBuffer[i].shouldSend = false;
    }

    Serial.println("\n========================================");
    Serial.println("Earthquake Monitoring System - ESP8266");
    Serial.println("Sensor: SW-420 Vibration Sensor");
    Serial.println("========================================");
    Serial.println("Server: " + String(serverUrl));
    Serial.println("Device UUID: " + String(deviceUuid));
    Serial.println("Send Threshold: Magnitude >= " + String(MAGNITUDE_THRESHOLD));
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

    // Cek jika magnitude >= 3.0 (gempa terdeteksi)
    if (magnitude >= MAGNITUDE_THRESHOLD) {
        unsigned long currentTime = millis();

        // Cek cooldown period
        if (!earthquakeDetected && (currentTime - lastDetectionTime >= DETECTION_COOLDOWN)) {
            earthquakeDetected = true;
            lastDetectionTime = currentTime;

            // Simpan data ke buffer
            saveToBuffer(vibration, magnitude, true);

            Serial.println("\n=== EARTHQUAKE DETECTED ===");
            Serial.print("Vibration: ");
            Serial.println(vibration);
            Serial.print("Magnitude: ");
            Serial.println(magnitude);
            Serial.print("Status: ");
            Serial.println(getStatusFromMagnitude(magnitude));
            Serial.println("==========================\n");

            // Trigger local alert
            triggerLocalAlert(magnitude);

            // Kirim data ke server
            sendDataToServer(vibration, magnitude);
        }
    } else {
        earthquakeDetected = false;

        // Untuk monitoring, simpan data ringan ke buffer (tapi jangan kirim)
        unsigned long currentTime = millis();
        static unsigned long lastSampleTime = 0;

        // Sample data setiap 10 detik untuk logging lokal
        if (currentTime - lastSampleTime >= 10000) {
            lastSampleTime = currentTime;
            saveToBuffer(vibration, magnitude, false); // shouldSend = false

            Serial.print(".");
            if (random(10) == 0) { // Log sesekali
                Serial.print("Vib: ");
                Serial.print(vibration);
                Serial.print(" Mag: ");
                Serial.println(magnitude, 2);
            }
        }
    }

    // Kirim heartbeat
    unsigned long currentTime = millis();
    if (currentTime - lastHeartbeat >= HEARTBEAT_INTERVAL) {
        sendHeartbeat();
        lastHeartbeat = currentTime;

        // Print status
        printStatus();
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
    updateStatusLED(currentMagnitude);

    delay(100); // Main loop delay
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
    // Sama dengan fungsi convertToMagnitude di Laravel
    if (vibration < 50) return 0; // No vibration

    if (vibration < 200) {
        return (vibration / 200.0) * 2.0; // 0-2 magnitude
    }

    if (vibration < 500) {
        return 2.0 + ((vibration - 200) / 300.0) * 3.0; // 2-5 magnitude
    }

    return 5.0 + ((vibration - 500) / 523.0) * 5.0; // 5-10 magnitude
}

String getStatusFromMagnitude(float magnitude) {
    if (magnitude >= 7.0) return "critical";
    if (magnitude >= 5.0) return "danger";
    if (magnitude >= 3.0) return "warning";
    return "normal";
}

// ==================== DATA BUFFER ====================
void saveToBuffer(int vibration, float magnitude, bool shouldSend) {
    if (bufferIndex < DATA_BUFFER_SIZE) {
        dataBuffer[bufferIndex].vibration = vibration;
        dataBuffer[bufferIndex].magnitude = magnitude;
        dataBuffer[bufferIndex].timestamp = getCurrentTimestamp();
        dataBuffer[bufferIndex].shouldSend = shouldSend;
        bufferIndex++;
    }

    // Jika buffer penuh, cek apakah ada data yang perlu dikirim
    if (bufferIndex >= DATA_BUFFER_SIZE) {
        processBuffer();
        bufferIndex = 0;
    }
}

void processBuffer() {
    bool hasEarthquakeData = false;

    // Cek jika ada data dengan magnitude >= 3.0
    for (int i = 0; i < DATA_BUFFER_SIZE; i++) {
        if (dataBuffer[i].shouldSend) {
            hasEarthquakeData = true;
            break;
        }
    }

    // Jika ada data gempa, kirim semua data di buffer
    if (hasEarthquakeData) {
        sendBulkData();
    }

    // Reset buffer
    for (int i = 0; i < DATA_BUFFER_SIZE; i++) {
        dataBuffer[i].vibration = 0;
        dataBuffer[i].magnitude = 0.0;
        dataBuffer[i].timestamp = "";
        dataBuffer[i].shouldSend = false;
    }
}

// ==================== SERVER COMMUNICATION ====================
bool sendDataToServer(int vibration, float magnitude) {
    HTTPClient http;
    WiFiClient client;

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/data";

    Serial.println("Sending earthquake data to server...");
    Serial.println("URL: " + url);

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    // Buat payload sesuai controller Laravel
    StaticJsonDocument<256> doc;
    doc["vibration"] = vibration;
    doc["status"] = "online";
    doc["timestamp"] = getCurrentTimestamp();
    doc["battery"] = 95; // Simulasi battery level

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.println("Request: " + requestBody);

    int httpCode = http.POST(requestBody);

    bool success = false;

    if (httpCode > 0) {
        String response = http.getString();
        Serial.println("Response (" + String(httpCode) + "): " + response);

        // Parse response
        DynamicJsonDocument responseDoc(1024);
        DeserializationError error = deserializeJson(responseDoc, response);

        if (!error) {
            if (responseDoc["success"]) {
                success = true;
                Serial.println("✓ Data sent successfully!");

                // Log detail jika ada earthquake event
                if (responseDoc["data"]["earthquake_event"]) {
                    Serial.println("Earthquake Event Created:");
                    Serial.print("  Magnitude: ");
                    Serial.println(responseDoc["data"]["earthquake_event"]["magnitude"].as<float>());
                    Serial.print("  Status: ");
                    Serial.println(responseDoc["data"]["earthquake_event"]["status"].as<String>());
                    Serial.print("  Alert Type: ");
                    Serial.println(responseDoc["data"]["earthquake_event"]["alert_type"].as<String>());
                }
            } else {
                Serial.print("Server error: ");
                Serial.println(responseDoc["message"].as<String>());
            }
        }
    } else {
        Serial.print("HTTP Error: ");
        Serial.println(http.errorToString(httpCode).c_str());
    }

    http.end();
    return success;
}

void sendBulkData() {
    if (bufferIndex == 0) return;

    HTTPClient http;
    WiFiClient client;

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/bulk-upload";

    Serial.println("Sending bulk data (" + String(bufferIndex) + " records)...");

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    StaticJsonDocument<1024> doc;
    JsonArray logs = doc.createNestedArray("logs");

    int sendCount = 0;
    for (int i = 0; i < bufferIndex; i++) {
        if (dataBuffer[i].shouldSend) { // Hanya kirim yang magnitude >= 3.0
            JsonObject log = logs.createNestedObject();
            log["vibration"] = dataBuffer[i].vibration;
            log["status"] = "online";
            log["timestamp"] = dataBuffer[i].timestamp;
            sendCount++;
        }
    }

    if (sendCount == 0) {
        Serial.println("No earthquake data to send in bulk");
        http.end();
        return;
    }

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.println("Bulk request (" + String(sendCount) + " earthquake records)");

    int httpCode = http.POST(requestBody);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.println("Bulk response: " + response);
    } else {
        Serial.print("Bulk upload failed: ");
        Serial.println(http.errorToString(httpCode).c_str());
    }

    http.end();
}

void sendHeartbeat() {
    HTTPClient http;
    WiFiClient client;

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/heartbeat";

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<128> doc;
    doc["status"] = "online";
    doc["battery"] = 95; // Simulasi
    doc["temperature"] = 27.5;
    doc["humidity"] = 65.0;

    String requestBody;
    serializeJson(doc, requestBody);

    int httpCode = http.POST(requestBody);

    if (httpCode > 0) {
        Serial.println("Heartbeat sent");
    } else {
        Serial.print("Heartbeat failed: ");
        Serial.println(http.errorToString(httpCode).c_str());
    }

    http.end();
}

// ==================== LOCAL ALERTS ====================
void triggerLocalAlert(float magnitude) {
    Serial.println("ALERT! Magnitude: " + String(magnitude, 1));

    String status = getStatusFromMagnitude(magnitude);

    if (status == "critical") {
        // Critical alert - pola sangat cepat
        for(int i = 0; i < 15; i++) {
            digitalWrite(LED_PIN, HIGH);
            tone(BUZZER_PIN, 2000, 80);
            delay(100);
            digitalWrite(LED_PIN, LOW);
            delay(50);
        }
    } else if (status == "danger") {
        // Danger alert - pola cepat
        for(int i = 0; i < 10; i++) {
            digitalWrite(LED_PIN, HIGH);
            tone(BUZZER_PIN, 1500, 100);
            delay(150);
            digitalWrite(LED_PIN, LOW);
            delay(100);
        }
    } else if (status == "warning") {
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
        if (currentTime - lastBlink >= 100) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    } else if (magnitude >= 5.0) {
        // Danger - blink cepat
        if (currentTime - lastBlink >= 200) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    } else if (magnitude >= 3.0) {
        // Warning - blink sedang
        if (currentTime - lastBlink >= 500) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    } else {
        // Normal - LED hidup pelan
        if (currentTime - lastBlink >= 2000) {
            digitalWrite(LED_PIN, !digitalRead(LED_PIN));
            lastBlink = currentTime;
        }
    }
}

// ==================== MANUAL TEST ====================
void manualTest() {
    Serial.println("\n=== MANUAL TEST ===");

    // Test dengan magnitude 4.5 (warning)
    HTTPClient http;
    WiFiClient client;

    String url = String(serverUrl) + "/api/v1/devices/" + String(deviceUuid) + "/test-detection";

    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<128> doc;
    doc["test_magnitude"] = 4.5;

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.println("Test request: " + requestBody);

    int httpCode = http.POST(requestBody);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.println("Test response: " + response);
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

// ==================== WIFI FUNCTIONS ====================
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

        // Success pattern
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

// ==================== UTILITY FUNCTIONS ====================
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
    Serial.println("Starting Earthquake Monitoring System...");

    for(int i = 0; i < 3; i++) {
        digitalWrite(LED_PIN, HIGH);
        tone(BUZZER_PIN, 1000, 100);
        delay(200);
        digitalWrite(LED_PIN, LOW);
        delay(200);
    }

    // Special earthquake monitoring pattern
    for(int i = 0; i < 2; i++) {
        digitalWrite(LED_PIN, HIGH);
        tone(BUZZER_PIN, 800, 300);
        delay(400);
        digitalWrite(LED_PIN, LOW);
        delay(200);
    }
}

void printStatus() {
    Serial.println("\n--- Device Status ---");
    Serial.print("WiFi: ");
    Serial.println(WiFi.status() == WL_CONNECTED ? "Connected" : "Disconnected");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());
    Serial.print("RSSI: ");
    Serial.println(WiFi.RSSI());
    Serial.print("Buffer: ");
    Serial.print(bufferIndex);
    Serial.print("/");
    Serial.println(DATA_BUFFER_SIZE);
    Serial.print("Last Magnitude: ");
    Serial.println(currentMagnitude, 2);
    Serial.println("-------------------\n");
}
