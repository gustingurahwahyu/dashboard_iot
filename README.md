# Smart Inventory Management System

Smart Inventory Management System berbasis RFID (ESP32 + RC522) dengan backend Laravel 11, SQLite, Blade, dan Tailwind CSS. Dashboard melakukan polling data setiap 3 detik tanpa WebSocket.

## Fitur Utama

- Dashboard statistik: total barang, ADA, KELUAR
- Manajemen barang dengan pencarian dan filter status
- Log aktivitas masuk dan keluar
- Scan RFID dengan form registrasi untuk UID baru
- Polling real-time menggunakan Fetch API

## Tech Stack

- Laravel 11
- SQLite
- Blade + Tailwind CSS
- Polling setiap 3 detik

## Setup Lokal

1. Install dependencies PHP:
	```bash
	composer install
	```
2. Install dependencies Node:
	```bash
	npm install
	```
3. Pastikan file SQLite ada:
	```bash
	type nul > database\database.sqlite
	```
4. Jalankan migrasi:
	```bash
	php artisan migrate
	```
5. Jalankan server:
	```bash
	php artisan serve
	```
6. Jalankan Vite:
	```bash
	npm run dev
	```

## Endpoint API

- GET /api/barang
- POST /api/barang
- PATCH /api/barang/{id}
- GET /api/log
- POST /api/rfid/scan (butuh header X-API-KEY)

## Keamanan RFID (LAN lokal)

Tambahkan header `X-API-KEY` untuk akses `/api/rfid/scan`.

Contoh:
```http
POST http://<ip-server>:8000/api/rfid/scan
X-API-KEY: rfid-local-key-2026
Content-Type: application/json

{"uid":"04A1B2C3D4"}
```

## Halaman

- /dashboard
- /barang
- /log
- /scan
