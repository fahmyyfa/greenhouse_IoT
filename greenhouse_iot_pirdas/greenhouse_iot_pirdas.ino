/*********************************************************
 * GREENHOUSE ESP32 - STABLE FINAL LOGIC
 *********************************************************/

#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <ESP32Servo.h>
#include <ArduinoJson.h>

/**************** WIFI ****************/
const char* ssid = "iPhone (4)";
const char* password = "123456789";

/**************** SERVER ****************/
const char* SERVER_SENSOR  = "http://172.20.10.5/greenhouse/api/store_sensor.php";
const char* SERVER_CONTROL = "http://172.20.10.5/greenhouse/api/get_control.php";

/**************** PIN ****************/
#define DHTPIN     4
#define DHTTYPE    DHT11
#define RAIN_PIN   27

#define RELAY_FAN  26     // ACTIVE LOW
#define RELAY_LAMP 33     // ACTIVE LOW (dipaksa via logic)
#define SERVO_PIN  25

/**************** OBJECT ****************/
DHT dht(DHTPIN, DHTTYPE);
Servo ventilasi;

/**************** VARIABLE ****************/
float suhu = 0;
float kelembapan = 0;
bool hujan = false;

bool manualMode = false;

unsigned long lastMillis = 0;
const unsigned long interval = 5000;

/**************** SETUP ****************/
void setup() {
  Serial.begin(115200);

  pinMode(RAIN_PIN, INPUT);
  pinMode(RELAY_FAN, OUTPUT);
  pinMode(RELAY_LAMP, OUTPUT);

  // Relay ACTIVE LOW → default OFF
  digitalWrite(RELAY_FAN, HIGH);
  digitalWrite(RELAY_LAMP, HIGH);

  ventilasi.attach(SERVO_PIN);
  ventilasi.write(90);

  dht.begin();

  WiFi.begin(ssid, password);
  Serial.print("WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected");
}

/**************** LOOP ****************/
void loop() {
  if (millis() - lastMillis >= interval) {
    lastMillis = millis();

    bacaSensor();
    kontrolLogic();
    kirimSensor();
  }
}

/**************** SENSOR ****************/
void bacaSensor() {
  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (!isnan(t)) suhu = t;
  if (!isnan(h)) kelembapan = h;

  hujan = digitalRead(RAIN_PIN) == LOW;

  Serial.print("Suhu: ");
  Serial.print(suhu);
  Serial.print(" | Hum: ");
  Serial.print(kelembapan);
  Serial.print(" | Hujan: ");
  Serial.println(hujan ? "YA" : "TIDAK");
}

/**************** CONTROL ****************/
void kontrolLogic() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(SERVER_CONTROL);
  int code = http.GET();

  if (code == 200) {
    DynamicJsonDocument doc(256);
    deserializeJson(doc, http.getString());

    manualMode = doc["mode"] == 1;

    int kipas = doc["kipas"];
    int lampu = doc["lampu"];
    int servo = doc["servo"];

    if (manualMode) {
      setRelay(RELAY_FAN, kipas);
      setRelay(RELAY_LAMP, lampu);
      ventilasi.write(servo);
    } else {
      modeOtomatis();
    }
  }

  http.end();
}

/**************** AUTO MODE ****************/
void modeOtomatis() {
  if (manualMode) return; // ⛔ STOP AUTO TOTAL

  if (hujan) {
    ventilasi.write(0);
    setRelay(RELAY_FAN, 0);
    setRelay(RELAY_LAMP, 1);   // HUJAN → LAMPU WAJIB NYALA
  } else {
    ventilasi.write(90);
    setRelay(RELAY_FAN, suhu > 30);
    setRelay(RELAY_LAMP, 0);
  }
}

/**************** RELAY HANDLER ****************/
void setRelay(int pin, bool state) {
  // ACTIVE LOW
  digitalWrite(pin, state ? LOW : HIGH);
}

/**************** POST DB ****************/
void kirimSensor() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(SERVER_SENSOR);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String data =
    "suhu=" + String(suhu) +
    "&kelembapan=" + String(kelembapan) +
    "&hujan=" + String(hujan ? 1 : 0) +
    "&kipas=" + String(digitalRead(RELAY_FAN) == LOW) +
    "&lampu=" + String(digitalRead(RELAY_LAMP) == LOW) +
    "&servo=" + String(ventilasi.read());

  http.POST(data);
  http.end();

  Serial.println("POST -> " + data);
}
