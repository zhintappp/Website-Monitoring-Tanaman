#include <LiquidCrystal_I2C.h>
#include <Wire.h>
#include "DHT.h"
#include <WiFi.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <HTTPClient.h>
#include <WifiClient.h>
#define SOIL_PIN 35
#define DHTPIN 14
#define DHTTYPE DHT11
#define RELAY_PIN 5

LiquidCrystal_I2C lcd(0x27, 16, 2);
DHT dht(DHTPIN, DHTTYPE);

float kelembabanTanah = 0;
float Temperature = 0;
float Humidity = 0;

float kelembaban[3];
float suhu[3];
float rule[3][3];
float validasi, pembilang, penyebut;
float output;

float L_Sebentar = 3.75;
float L_Normal = 2.5;
float L_Lama = 3.75;
float TT_Sebentar = 3.75;
float TT_Normal = 7.5;
float TT_Lama = 11.25;

const char* ssid = "Irmaaaaa";
const char* password = "123sampe5aja";
// String serverName = "http://maritumbuhbersama.my.id/pilih.php"; 
// String Nameserver = "http://maritumbuhbersama.my.id/update_sensor.php"; 
String jenengserver = "http://maritumbuhbersama.my.id/insert_sensor.php";

const long utcOffsetInSeconds = 25200;
//const long customOffsetSeconds = -9360;

WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", utcOffsetInSeconds); 
//+ customOffsetSeconds );

WiFiClient client; 
HTTPClient http;

unsigned long previousMillis = 0;
const long interval = 5000; // Interval pembacaan sensor (dalam milidetik)

bool pumpActivated = false; // Flag to track pump activation

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      Serial.print("connecting brow...");
  }
  Serial.println("CONNECTED");

  timeClient.begin();
  lcd.begin();
  lcd.backlight();
  pinMode(SOIL_PIN, INPUT);
  dht.begin();
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, HIGH);
}

void nilaiKelembaban() {
  float soil = analogRead(SOIL_PIN);
  float sensorTanah = map(soil, 4095, 946, 0, 100); 

  Serial.print("Kelembaban Tanah: ");
  Serial.print(kelembabanTanah);
  Serial.println("%");
}

void nilaiSuhu() {
  Temperature = dht.readTemperature();
  Humidity = dht.readHumidity();

  Serial.print("Suhu: ");
  Serial.print(Temperature);
  Serial.print("°C, Kelembaban: ");
  Serial.print(Humidity);
  Serial.println("%");
}

void fuzzyTemperature() {
  if (Temperature < 20) {
    suhu[0] = 1;
    suhu[1] = 0;
    suhu[2] = 0;
  } else if (Temperature >= 20 && Temperature <= 30) {
    suhu[0] = (30 - Temperature) / 15;
    suhu[1] = (Temperature - 20) / 10;
    suhu[2] = 0;
  } else if (Temperature >= 30 && Temperature <= 40) {
    suhu[0] = 0;
    suhu[1] = (40 - Temperature) / 10;
    suhu[2] = (Temperature - 30) / 15;
  } else {
    suhu[0] = 0;
    suhu[1] = 0;
    suhu[2] = 1;
  }
}

void fuzzyKelembabanTanah() {
  if (kelembabanTanah < 25) {
    kelembaban[0] = 1;
    kelembaban[1] = 0;
    kelembaban[2] = 0;
  } else if (kelembabanTanah >= 25 && kelembabanTanah <= 50) {
    kelembaban[0] = (50 - kelembabanTanah) / 30;
    kelembaban[1] = (kelembabanTanah - 25) / 25;
    kelembaban[2] = 0;
  } else if (kelembabanTanah >= 50 && kelembabanTanah <= 75) {
    kelembaban[0] = 0;
    kelembaban[1] = (75 - kelembabanTanah) / 25;
    kelembaban[2] = (kelembabanTanah - 50) / 30;
  } else {
    kelembaban[0] = 0;
    kelembaban[1] = 0;
    kelembaban[2] = 1;
  }
}

void rule_evaluasi() {
  for (int i = 0; i < 3; i++) {
    for (int j = 0; j < 3; j++) {
      if (suhu[i] < kelembaban[j]) {
        rule[i][j] = suhu[i];
      } else {
        rule[i][j] = kelembaban[j];
      }
    }
  }
}

void display() {
  lcd.setCursor(0, 0);
  lcd.print("Suhu: ");
  lcd.print(Temperature);
  lcd.print(" C");
  lcd.setCursor(0, 1);
  lcd.print("K.Tanah: ");
  lcd.print(kelembabanTanah);
  lcd.print("%");

  delay(2000);
}

void defuzzifikasi() {
  pembilang = (rule[0][0] * L_Lama * TT_Lama) + (rule[0][1] * L_Sebentar * TT_Sebentar) +
              (rule[0][2] * L_Sebentar * TT_Sebentar) +
              (rule[1][0] * L_Lama * TT_Lama) + (rule[1][1] * L_Normal * TT_Normal) +
              (rule[1][2] * L_Sebentar * TT_Sebentar) +
              (rule[2][0] * L_Lama * TT_Lama) + (rule[2][1] * L_Normal * TT_Normal) +
              (rule[2][2] * L_Sebentar * TT_Sebentar);

  penyebut = (rule[0][0] * L_Lama) + (rule[0][1] * L_Sebentar) +
             (rule[0][2] * L_Sebentar) +
             (rule[1][0] * L_Lama) + (rule[1][1] * L_Normal) + (rule[1][2] * L_Sebentar) +
             (rule[2][0] * L_Lama) + (rule[2][1] * L_Normal) + (rule[2][2] * L_Sebentar);

  validasi = pembilang / penyebut;
  output = validasi;
}


void loop() {
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= interval) {
    // Lakukan pembacaan sensor di sini
    nilaiKelembaban();
    nilaiSuhu();
    
    previousMillis = currentMillis;

    display(); 
  }
  
  timeClient.update(); 

  String formattedTime = timeClient.getFormattedTime();
  Serial.print("Waktu NTP: ");
  Serial.println(formattedTime);
  delay(1000);

  int currentHour = timeClient.getHours();
  int currentMinute = timeClient.getMinutes();
  int currentSecond = timeClient.getSeconds();

  if (currentHour == 8 && currentMinute == 0 && currentSecond == 0) {
    if (!pumpActivated) {
      fuzzyTemperature();
      fuzzyKelembabanTanah();
      rule_evaluasi();
      defuzzifikasi();
      display();

      Serial.println("Hasil Defuzzifikasi: ");
      Serial.println(output);
      //delay(1000);

      digitalWrite(RELAY_PIN, LOW);
      delay(output * 1000);

      digitalWrite(RELAY_PIN, HIGH);
      Serial.println("Pompa air OFF");

      pumpActivated = true; // Set the flag to indicate the pump has been activated
    }
  } else if (currentHour == 8 && currentMinute == 0 && currentSecond == 0) {
      pumpActivated = false; // Reset the flag once the time window has passed
      digitalWrite(RELAY_PIN, HIGH); // Ensure the pump remains off at other times
 }

  if (currentHour == 16 && currentMinute == 0 && currentSecond == 0) {
    if (!pumpActivated) {
      fuzzyTemperature();
      fuzzyKelembabanTanah();
      rule_evaluasi();
      defuzzifikasi();
      display();

      Serial.println("Hasil Defuzzifikasi: ");
      Serial.println(output);
      delay(1000);

      digitalWrite(RELAY_PIN, LOW);
      delay(output * 1000);

      digitalWrite(RELAY_PIN, HIGH);
      Serial.println("Pompa air OFF");

      pumpActivated = true; // Set the flag to indicate the pump has been activated
    }
  } else if (currentHour == 16 && currentMinute == 0 && currentSecond == 0) {
      pumpActivated = false; // Reset the flag once the time window has passed
      digitalWrite(RELAY_PIN, HIGH); // Ensure the pump remains off at other times
 }

  if (isnan(Temperature) || isnan(Humidity) || isnan(kelembabanTanah)) {
    Serial.println("Error: Data tidak valid");
    return; // Jangan kirim data jika tidak valid
  }
  
  String payload = "suhu_u=" + String(Temperature) + "&kelem_t=" + String(kelembabanTanah) + "&durasi_penyiraman=" + String(output); 
 
  http.begin(client, jenengserver); 
  http.addHeader("Content-Type", "application/x-www-form-urlencoded"); 
 
  http.setFollowRedirects(HTTPC_DISABLE_FOLLOW_REDIRECTS); 
  int httpResponseCode2 = http.POST(payload); 
  if (httpResponseCode2 > 0) { 
  Serial.println("Data terkirim ke server jenengserver"); 
  Serial.println(httpResponseCode2); 
  } else { 
  Serial.println("Gagal mengirimkan data ke server jenengserver"); 
  } 
  http.end(); 
  delay(3000);

}