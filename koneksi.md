# 📡 Dokumentasi Koneksi ESP32 + RFID ke Sistem Smart Inventory

## 📋 Daftar Isi
1. [Ringkasan Sistem](#ringkasan-sistem)
2. [Arsitektur Koneksi](#arsitektur-koneksi)
3. [Hardware Setup](#hardware-setup)
4. [Konfigurasi Sistem](#konfigurasi-sistem)
5. [Implementasi ESP32](#implementasi-esp32)
6. [API Integration](#api-integration)
7. [Testing & Troubleshooting](#testing--troubleshooting)
8. [Contoh Implementasi Lengkap](#contoh-implementasi-lengkap)

---

## 📌 Ringkasan Sistem

**Smart Inventory** adalah sistem manajemen barang berbasis web yang menggunakan RFID untuk tracking real-time. Setiap kali ESP32 membaca tag RFID, data dikirim ke server untuk:

- ✅ Mencatat posisi barang (ADA/KELUAR)
- ✅ Membuat log aktivitas otomatis
- ✅ Memperbarui statistik inventory
- ✅ Menampilkan notifikasi real-time

**Tech Stack:**
- Backend: Laravel 11 + SQLite
- Frontend: Blade + Tailwind CSS + JavaScript Polling
- API: RESTful dengan API Key authentication
- Database: Barang (items), LogBarang (activity logs), Users

---

## 🔗 Arsitektur Koneksi

```
┌──────────────────┐
│     ESP32        │
│  + RFID Reader   │
│  + WiFi Module   │
└────────┬─────────┘
         │ HTTP POST
         │ (UID + Metadata)
         ↓
┌──────────────────────────────┐
│   Laravel 11 Backend          │
│  POST /api/rfid/scan          │
│  (dengan X-API-KEY header)    │
└────────┬─────────────────────┘
         │
         ↓
┌──────────────────────────────┐
│    SQLite Database            │
│  - Barang (items)             │
│  - LogBarang (audit trail)    │
│  - Cache (scan results)       │
└──────────────────────────────┘
         │
         ↓
┌──────────────────────────────┐
│   Frontend Dashboard          │
│  - Polling setiap 3 detik     │
│  - Tampil notifikasi real-time│
└──────────────────────────────┘
```

**Data Flow:**
```
Tag dibaca → ESP32 membaca UID → Kirim ke /api/rfid/scan → 
Server check item → Toggle status → Buat log → Cache hasil → 
Frontend notifikasi user
```

---

## 🔧 Hardware Setup

### Komponen yang Dibutuhkan

| Komponen | Spesifikasi | Fungsi |
|----------|-------------|--------|
| **ESP32** | WROOM-32 atau Wrover | Microcontroller utama + WiFi |
| **RFID Reader** | MFRC522 (SPI) | Membaca tag RFID 125kHz/13.56MHz |
| **RFID Tags** | Mifare Classic 1K / EM4100 | Target yang akan dipindai |
| **Power Supply** | 5V 2A USB atau Power Bank | Sumber daya ESP32 |
| **Kabel Jumper** | Male-to-male/female | Koneksi antar komponen |
| **Breadboard** | Opsional | Memudahkan koneksi |

### Wiring Diagram - MFRC522 ke ESP32 (SPI Mode)

```
MFRC522 Pin    ESP32 Pin    Deskripsi
─────────────────────────────────────
VCC      →     5V           Power (atau 3.3V)
GND      →     GND          Ground
SCK      →     GPIO18       SPI Clock
MOSI     →     GPIO23       SPI MOSI (Master Out Slave In)
MISO     →     GPIO19       SPI MISO (Master In Slave Out)
SDA/CS   →     GPIO5        Chip Select (SS/CS)
RST      →     GPIO17       Reset Pin
```

**Alternatif Pin Configuration (Jika GPIO sudah dipakai):**
```
// Jika GPIO18, GPIO23, GPIO19 tidak tersedia, ubah di code:
#define SCK_PIN     14
#define MOSI_PIN    12
#define MISO_PIN    13
#define CS_PIN      15
#define RST_PIN     2
```

### Koneksi Fisik dengan Breadboard

```
┌─ VCC (5V) ─────→ MFRC522 VCC
│
├─ GND ──────────→ MFRC522 GND
│
├─ GPIO18 ──────→ MFRC522 SCK
├─ GPIO23 ──────→ MFRC522 MOSI
├─ GPIO19 ──────→ MFRC522 MISO
├─ GPIO5 ───────→ MFRC522 SDA/CS
└─ GPIO17 ──────→ MFRC522 RST

ESP32
```

**Tips:**
- Gunakan **5V** untuk power MFRC522 jika tersedia, tapi GPIO harus 5V tolerant
- Jika hanya 3.3V: gunakan level shifter atau cek datasheet MFRC522
- Pastikan GND terhubung ke semua komponen

---

## ⚙️ Konfigurasi Sistem

### 1️⃣ Setup Environment Variable (Laravel)

Edit file `.env` di root project:

```bash
# Hapus # untuk mengaktifkan
# ─────────────────────────────
RFID_API_KEY=your_secure_api_key_here_12345
```

**Generate API Key yang aman:**
```bash
# Di terminal Laravel project:
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Contoh output: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`

### 2️⃣ Verify Middleware Setup

Pastikan file `app/Http/Middleware/VerifyRfidApiKey.php` sudah ada dan benar:

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyRfidApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');
        $expectedKey = config('services.rfid_api_key') ?? env('RFID_API_KEY');

        if (!$apiKey || !hash_equals($expectedKey, $apiKey)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
```

### 3️⃣ Register Middleware di Routes

Edit `routes/web.php` atau `routes/api.php`:

```php
Route::middleware('verify.rfid.api.key')->post('/api/rfid/scan', [RFIDController::class, 'scan']);
```

---

## 📱 Implementasi ESP32

### Instalasi Library

**Menggunakan PlatformIO:**

```ini
# platformio.ini
[env:esp32doit-devkit-v1]
platform = espressif32
board = esp32doit-devkit-v1
framework = arduino
lib_deps =
    miguelbalboa/MFRC522 @ ^1.4.10
    WiFi
    HTTPClient
```

**Menggunakan Arduino IDE:**
1. Sketch → Include Library → Manage Libraries
2. Cari `MFRC522` by Miguel Balboa
3. Install versi terbaru

### Code ESP32 - Basic Scan & Send

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

// WiFi Configuration
const char* ssid = "YOUR_SSID";
const char* password = "YOUR_PASSWORD";
const char* serverUrl = "http://your-laravel-ip:8000/api/rfid/scan";
const char* apiKey = "your_secure_api_key_here_12345";

// RFID Pin Configuration
#define SCK_PIN   18
#define MOSI_PIN  23
#define MISO_PIN  19
#define CS_PIN    5
#define RST_PIN   17

MFRC522 mfrc522(CS_PIN, RST_PIN);

void setup() {
    Serial.begin(115200);
    delay(1000);
    
    Serial.println("\n\nSmart Inventory - ESP32 RFID Scanner");
    Serial.println("=====================================");
    
    // Initialize SPI
    SPI.begin(SCK_PIN, MISO_PIN, MOSI_PIN, CS_PIN);
    
    // Initialize MFRC522
    mfrc522.PCD_Init();
    mfrc522.PCD_DumpVersionToSerial();
    
    // Connect to WiFi
    connectToWiFi();
}

void loop() {
    // Check WiFi connection
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi disconnected, reconnecting...");
        connectToWiFi();
    }
    
    // Scan RFID Tag
    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
        String uid = getUidString();
        
        Serial.print("Tag detected! UID: ");
        Serial.println(uid);
        
        // Send to server
        sendToServer(uid);
        
        // Halt PICC reading
        mfrc522.PICC_HaltA();
        
        delay(500); // Debounce delay
    }
    
    delay(100);
}

void connectToWiFi() {
    Serial.print("Connecting to WiFi: ");
    Serial.println(ssid);
    
    WiFi.mode(WIFI_STA);
    WiFi.begin(ssid, password);
    
    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) {
        delay(500);
        Serial.print(".");
        attempts++;
    }
    
    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\nWiFi Connected!");
        Serial.print("IP: ");
        Serial.println(WiFi.localIP());
    } else {
        Serial.println("\nWiFi Failed!");
    }
}

String getUidString() {
    String uidStr = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
        if (mfrc522.uid.uidByte[i] < 0x10) {
            uidStr += "0";
        }
        uidStr += String(mfrc522.uid.uidByte[i], HEX);
    }
    uidStr.toUpperCase();
    return uidStr;
}

void sendToServer(String uid) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi not connected!");
        return;
    }
    
    HTTPClient http;
    
    // Set timeout
    http.setTimeout(5000);
    
    // Begin request
    http.begin(serverUrl);
    
    // Add headers
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-KEY", apiKey);
    
    // Create JSON payload
    String payload = "{\"uid\": \"" + uid + "\"}";
    
    Serial.print("Sending: ");
    Serial.println(payload);
    
    // Send POST request
    int httpResponseCode = http.POST(payload);
    
    Serial.print("HTTP Response: ");
    Serial.println(httpResponseCode);
    
    // Handle response
    if (httpResponseCode > 0) {
        String response = http.getString();
        Serial.print("Response: ");
        Serial.println(response);
        
        // Parse response and show status
        if (response.indexOf("\"status\":\"new\"") > 0) {
            Serial.println("✓ Item baru - harap registrasi di dashboard!");
            displayNewItemLed(); // Opsional: LED indicator
        } else if (response.indexOf("\"status\":\"ADA\"") > 0) {
            Serial.println("✓ Item sekarang: ADA");
            displayFoundLed();
        } else if (response.indexOf("\"status\":\"KELUAR\"") > 0) {
            Serial.println("✓ Item sekarang: KELUAR");
            displayExitedLed();
        }
    } else {
        Serial.print("Error: ");
        Serial.println(httpResponseCode);
    }
    
    http.end();
}

// Opsional: LED indicator functions
void displayNewItemLed() {
    // Blink LED 3x untuk item baru
    // digitalWrite(LED_PIN, HIGH);
    // delay(100);
    // digitalWrite(LED_PIN, LOW);
}

void displayFoundLed() {
    // Single blink untuk item found
}

void displayExitedLed() {
    // Double blink untuk item keluar
}
```

### Code ESP32 - Advanced (dengan LCD Display)

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <LiquidCrystal_I2C.h>

// LCD I2C Address (0x27 atau 0x3F - sesuaikan dengan module Anda)
LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
    Serial.begin(115200);
    
    // Initialize LCD
    lcd.init();
    lcd.backlight();
    lcd.print("Smart Inventory");
    lcd.setCursor(0, 1);
    lcd.print("Initializing...");
    
    delay(2000);
    lcd.clear();
    
    // ... rest of setup code ...
}

void loop() {
    // ... scanning code ...
    
    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
        String uid = getUidString();
        
        lcd.clear();
        lcd.print("Scan detected!");
        lcd.setCursor(0, 1);
        lcd.print(uid.substring(0, 16)); // Show first 16 chars
        
        sendToServerWithDisplay(uid);
        
        mfrc522.PICC_HaltA();
        delay(500);
    }
}

void sendToServerWithDisplay(String uid) {
    lcd.clear();
    lcd.print("Sending...");
    
    HTTPClient http;
    http.setTimeout(5000);
    http.begin(serverUrl);
    
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-KEY", apiKey);
    
    String payload = "{\"uid\": \"" + uid + "\"}";
    int httpResponseCode = http.POST(payload);
    
    lcd.clear();
    
    if (httpResponseCode == 200) {
        String response = http.getString();
        
        if (response.indexOf("\"status\":\"new\"") > 0) {
            lcd.print("Item Baru!");
            lcd.setCursor(0, 1);
            lcd.print("Register di web");
        } else if (response.indexOf("\"status\":\"ADA\"") > 0) {
            lcd.print("Status: ADA");
            lcd.setCursor(0, 1);
            lcd.print("Good!");
        } else if (response.indexOf("\"status\":\"KELUAR\"") > 0) {
            lcd.print("Status: KELUAR");
            lcd.setCursor(0, 1);
            lcd.print("Item out!");
        }
    } else {
        lcd.print("Error: ");
        lcd.print(httpResponseCode);
        lcd.setCursor(0, 1);
        lcd.print("Check WiFi");
    }
    
    delay(2000);
    lcd.clear();
    lcd.print("Waiting...");
}
```

---

## 🔌 API Integration

### Endpoint: POST /api/rfid/scan

**URL:** `http://your-server-ip:8000/api/rfid/scan`

**Method:** POST

**Headers:**
```
Content-Type: application/json
X-API-KEY: your_secure_api_key_here_12345
```

**Request Body:**
```json
{
  "uid": "A1B2C3D4"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "status": "ADA",          // atau "KELUAR" atau "new"
  "uid": "A1B2C3D4",
  "nama_barang": "Monitor LG",
  "kategori": "Elektronik",
  "last_seen": "2026-05-21 14:30:45",
  "aksi": "MASUK",          // Aksi yang baru dicatat
  "message": "Item status updated",
  "items": [...],           // Array semua items
  "meta": {
    "stats": {
      "total": 15,
      "ada": 10,
      "keluar": 5
    },
    "last_update": "2026-05-21T14:30:45Z",
    "last_scan": {...}
  }
}
```

**Response - New Item (200):**
```json
{
  "success": true,
  "status": "new",
  "uid": "A1B2C3D4",
  "message": "New item detected. Please register in dashboard.",
  "items": [...],
  "meta": {...}
}
```

**Response Error (401):**
```json
{
  "error": "Unauthorized",
  "message": "Invalid or missing X-API-KEY header"
}
```

**Response Error (500):**
```json
{
  "error": "Server Error",
  "message": "RFID_API_KEY not configured"
}
```

### Opsi Tambahan untuk Request

Untuk implementasi lebih lanjut, Anda bisa menambah field opsional:

```json
{
  "uid": "A1B2C3D4",
  "scan_timestamp": "2026-05-21T14:30:45Z",  // Opsional: waktu scan lokal
  "location": "Gate 1",                       // Opsional: lokasi scanner
  "device_id": "ESP32-001"                    // Opsional: ID device
}
```

**Catatan:** Server saat ini hanya memproses `uid`. Field tambahan disimpan untuk referensi atau enhancement masa depan.

---

## 🧪 Testing & Troubleshooting

### 1. Test dengan cURL (dari terminal)

```bash
# Format dasar
curl -X POST http://localhost:8000/api/rfid/scan \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your_secure_api_key_here_12345" \
  -d '{"uid":"A1B2C3D4"}'

# Test real dengan environment lokal
curl -X POST http://192.168.1.100:8000/api/rfid/scan \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: $(grep RFID_API_KEY .env | cut -d= -f2)" \
  -d '{"uid":"TestTag001"}'
```

### 2. Serial Monitor Debug dari ESP32

```
✓ Output yang diharapkan:
─────────────────────────
Smart Inventory - ESP32 RFID Scanner
=====================================
MFRC522 Software Version: 0x92
WiFi connecting.....................
WiFi Connected!
IP: 192.168.1.105
Waiting...
Tag detected! UID: A1B2C3D4
Sending: {"uid": "A1B2C3D4"}
HTTP Response: 200
Response: {"success":true,"status":"ADA"...}
✓ Item sekarang: ADA
```

### 3. Common Issues & Solutions

**Problem: "HTTP Response: 401"**
```
Penyebab: X-API-KEY header salah atau tidak ada
Solusi:
  1. Verifikasi RFID_API_KEY di .env file
  2. Pastikan header "X-API-KEY" tepat (case-sensitive)
  3. Tidak ada spasi tambahan di API key
```

**Problem: "WiFi Failed!"**
```
Penyebab: SSID/Password salah atau WiFi tidak ada
Solusi:
  1. Double-check SSID dan password (case-sensitive)
  2. Pastikan ESP32 dekat dengan router
  3. Verifikasi router mendukung 2.4GHz (ESP32 tidak support 5GHz)
```

**Problem: "Tag not detected"**
```
Penyebab: Wiring salah, pin configuration tidak sesuai, atau MFRC522 rusak
Solusi:
  1. Verifikasi semua koneksi SPI (SCK, MOSI, MISO)
  2. Test dengan contoh MFRC522 dari library: 
     File > Examples > MFRC522 > DumpInfo
  3. Pastikan power supply cukup (minimal 500mA)
```

**Problem: "Timeout" saat request ke server**
```
Penyebab: Server tidak accessible, firewall, atau URL salah
Solusi:
  1. Pastikan serverUrl benar: http://YOUR_IP:8000/api/rfid/scan
  2. Test ping ke server: ping YOUR_IP
  3. Verifikasi Laravel dev server running: php artisan serve --host 0.0.0.0
  4. Check firewall port 8000
```

**Problem: "Connection refused"**
```
Penyebab: Laravel server tidak berjalan
Solusi:
  1. Start Laravel: php artisan serve --host 0.0.0.0 --port 8000
  2. Atau gunakan task di VS Code: "Laravel Dev Server + Vite"
  3. Verifikasi port 8000 tidak dipakai proses lain
```

### 4. Checklist Debugging

```
[ ] WiFi terhubung (cek serial: "WiFi Connected!")
[ ] MFRC522 terbaca (cek versi di serial output)
[ ] API Key benar di .env
[ ] X-API-KEY header di request
[ ] Server URL benar (IP + port)
[ ] Laravel server running
[ ] Database migrations sudah jalan
[ ] Tag RFID valid (cek dengan contoh library)
[ ] Power supply cukup untuk MFRC522 + ESP32
```

---

## 💻 Contoh Implementasi Lengkap

### Full Setup Checklist

#### 1. Server Setup (Laravel)

```bash
# 1. Generate API Key
php -r "echo 'RFID_API_KEY=' . bin2hex(random_bytes(32)) . PHP_EOL;" >> .env

# 2. Migrate database
php artisan migrate

# 3. Seed demo data (optional)
php artisan db:seed --class=DemoInventorySeeder

# 4. Start server
php artisan serve --host 0.0.0.0 --port 8000
```

#### 2. ESP32 Setup

```cpp
// 1. Update credentials
const char* ssid = "Your_WiFi_SSID";
const char* password = "Your_WiFi_Password";
const char* serverUrl = "http://192.168.1.100:8000/api/rfid/scan";  // Ganti IP
const char* apiKey = "a1b2c3d4e5f6..."; // Copy dari .env

// 2. Compile & Upload ke ESP32
// PlatformIO: Ctrl+Shift+U
// Arduino IDE: Sketch > Upload

// 3. Monitor serial output
// Serial Monitor baud 115200
```

#### 3. First Scan Test

```
1. Buka dashboard: http://192.168.1.100:8000
2. Login dengan akun yang sudah dibuat
3. (Opsional) Seed demo data untuk test
4. Buka serial monitor ESP32
5. Scan tag RFID ke reader
6. Lihat output di serial monitor
7. Refresh dashboard - seharusnya ada notifikasi
8. Check "Log" tab untuk activity
```

### Scenario Testing

**Scenario 1: New Item Registration**
```
1. Scan tag RFID yang belum ada di system
2. Lihat "status": "new" di response
3. Di dashboard, akan ada notifikasi "New item detected"
4. User bisa form untuk register item (nama, kategori)
5. Scan ulang tag yang sama
6. Status berubah menjadi "ADA"
```

**Scenario 2: Item Movement Tracking**
```
1. Scan item yang sudah terdaftar (status: "ADA")
2. Response: "status": "KELUAR" (status berubah)
3. Log entry otomatis dibuat dengan aksi "KELUAR"
4. Scan ulang
5. Response: "status": "ADA" (status toggle kembali)
6. Log entry baru dengan aksi "MASUK"
```

**Scenario 3: Real-time Dashboard Update**
```
1. Buka dashboard di 2 browser/device berbeda
2. Scan item dari ESP32
3. Dashboard 1 & 2 otomatis update dalam 3 detik (polling)
4. Toast notification tampil
5. Table rows flash merah (status change)
6. Activity log update di tab "Log"
```

---

## 🚀 Enhancement Ideas

### Fitur Tambahan yang Bisa Ditambahkan

**1. Multi-location Tracking**
```cpp
// Send location dengan scan
String payload = "{\"uid\": \"" + uid + "\", \"location\": \"Gate 1\"}";
```

**2. Battery Monitoring**
```cpp
int batteryLevel = analogRead(A0);
float voltage = batteryLevel * (3.3 / 4095);
// Send ke server jika perlu
```

**3. LED Status Indicator**
```cpp
#define LED_NEW    GPIO2    // Biru - item baru
#define LED_FOUND  GPIO4    // Hijau - item found
#define LED_ERROR  GPIO15   // Merah - error

// Gunakan untuk visual feedback
```

**4. Buzzer Feedback**
```cpp
#define BUZZER_PIN GPIO21
tone(BUZZER_PIN, 1000, 200);  // Beep 1000Hz selama 200ms
```

**5. Offline Queue (untuk koneksi internet tidak stabil)**
```cpp
#include <SPIFFS.h>  // Flash storage

// Queue scan lokal jika tidak bisa connect
// Retry saat WiFi kembali online
```

**6. Advanced Configuration (via web interface)**
```cpp
// WiFi credentials bisa diset via AP WiFi bawaan
// Tidak perlu hard-code di source code
```

---

## 📞 Support & FAQ

**Q: Berapa jarak baca MFRC522 yang ideal?**
A: 0-10 cm untuk tag standar (bisa lebih jauh untuk antenna custom)

**Q: Bisa support multiple reader sekaligus?**
A: Ya! Setup multiple ESP32 dengan chip select berbeda, atau 1 ESP32 dengan multiple readers

**Q: Bagaimana jika internet putus?**
A: Implementasi offline queue di ESP32 untuk menyimpan scan lokal, sync saat internet kembali

**Q: Bisakah update status barang dari web dashboard?**
A: Ya, fitur sudah ada di PATCH /api/barang/{id} endpoint

**Q: Secure mana, API Key atau OAuth?**
A: API Key cukup untuk IoT lokal. Untuk multi-tenant, pertimbangkan OAuth2

---

## 📚 Referensi

- MFRC522 Library: https://github.com/miguelbalboa/MFRC522
- ESP32 Documentation: https://docs.espressif.com/projects/esp-idf/en/latest/esp32/
- Laravel API Documentation: https://laravel.com/docs/11/
- HTTP Client ESP32: https://github.com/espressif/arduino-esp32/tree/master/libraries/HTTPClient

---

**Terakhir diupdate:** May 21, 2026  
**Version:** 1.0  
**Kompatibel dengan:** Laravel 11 Smart Inventory
