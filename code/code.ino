#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

// --- Pin dan tipe sensor ---
#define DHTPIN 15
#define DHTTYPE DHT22
#define LED1 2
#define LED2 5

// --- Data WiFi dan alamat server ---
const char* ssid = "GAS";
const char* password = "1sampai8";

String serverPOST = "http://192.168.244.76/HTTP_CONN/post.php";
String serverGET  = "http://192.168.244.76/HTTP_CONN/get.php";

// --- Objek sensor DHT ---
DHT dht(DHTPIN, DHTTYPE);

// ==========================
// Fungsi untuk konek ke WiFi
// ==========================
void konekWiFi() {
  WiFi.begin(ssid, password);
  Serial.print("Nyambung ke WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\n✅ WiFi terhubung!");
  Serial.print("IP ESP32: ");
  Serial.println(WiFi.localIP());
}

// ======================================
// Fungsi untuk baca sensor suhu & lembap
// ======================================
void bacaSensor(float &suhu, float &kelembapan) {
  suhu = dht.readTemperature();
  kelembapan = dht.readHumidity();

  if (isnan(suhu) || isnan(kelembapan)) {
    Serial.println("⚠️ Sensor error! Gagal baca suhu/kelembapan");
    suhu = kelembapan = 0; // Kasih nilai default biar nggak crash
  }
}

// ======================================
// Fungsi kirim data suhu & kelembapan ke server (HTTP POST)
// ======================================
void kirimDataKeServer(float suhu, float kelembapan) {
  HTTPClient http;
  http.begin(serverPOST);
  http.addHeader("Content-Type", "application/json");

  // Format data jadi JSON
  String data = "{\"suhu\": " + String(suhu, 1) +
                ", \"kelembapan\": " + String(kelembapan, 1) + "}";

  int respon = http.POST(data);
  Serial.println("📤 Kirim data (POST), respon: " + String(respon));
  http.end();
}

// ======================================
// Fungsi ambil status LED dari server (HTTP GET)
// ======================================
void ambilStatusLED() {
  HTTPClient http;
  http.begin(serverGET);
  int respon = http.GET();

  if (respon == 200) {//200 status OK komunikasi HTTP
    String ledResponse = http.getString();  // <- ini variabel yang bagus
    Serial.println("📥 Ambil status LED (GET): " + ledResponse);

    // Cek isi respon, nyalakan atau matikan LED
    digitalWrite(LED1, ledResponse.indexOf("LED1=ON") >= 0 ? HIGH : LOW);
    digitalWrite(LED2, ledResponse.indexOf("LED2=ON") >= 0 ? HIGH : LOW);
  } else {
    Serial.println("⚠️ Gagal ambil status LED. Respon: " + String(respon));
  }

  http.end();
}


// ==========================
// Fungsi setup awal
// ==========================
void setup() {
  Serial.begin(115200);
  dht.begin();

  pinMode(LED1, OUTPUT);
  pinMode(LED2, OUTPUT);

  konekWiFi(); // Panggil koneksi WiFi
}

// ==========================
// Fungsi utama (loop)
// ==========================
void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    float suhu, kelembapan;
    bacaSensor(suhu, kelembapan);       // Baca sensor
    kirimDataKeServer(suhu, kelembapan); // Kirim POST ke server
    ambilStatusLED();                    // Ambil status LED dari server
  } else {
    Serial.println("🔄 WiFi putus, coba konek lagi...");
    konekWiFi(); // Coba nyambung ulang kalau putus
  }

  delay(10); // Tunggu 10 detik sebelum ngulang lagi
}
