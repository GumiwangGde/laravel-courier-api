# Courier Management RESTful API

RESTful API untuk pengelolaan master data kurir (Courier) yang dibangun menggunakan Laravel dan Pest PHP sesuai dengan spesifikasi teknis wawancara.

---

## Tech Stack

- **Framework:** Laravel 12.x / PHP 8.3+
- **Database:** SQLite (Default, zero external configuration)
- **Testing:** Pest PHP (`pestphp/pest`)
- **Code Style:** Laravel Pint (`laravel/pint`)

---

## Fitur Utama

- **CRUD Lengkap Kurir**: Endpoint `index`, `show`, `store`, `update`, dan `destroy`.
- **Tokenized Search**: Query `?search=budi+agung` mencocokkan nama `"Budiono Hadi Agung"` dengan memecah input menjadi token kata individual (tidak kaku dengan substring tunggal).
- **Level Filtering**: Mendukung filter kurir berdasarkan level (rentang level 1–5) dengan format string dipisahkan koma (`?level=2,3`) maupun array.
- **Dynamic Sorting**: Default sort berdasarkan nama kurir (A–Z), dengan opsi override pengurutan berdasarkan tanggal pendaftaran (`?sort_by=registered_at&order=desc`).
- **Pagination**: Output list otomatis menyertakan pagination meta (`current_page`, `per_page`, `total`, `links`).
- **Standardized Response**: Format response konsisten menggunakan Eloquent API Resource (`CourierResource`).
- **Robust Validation**: Validasi request terisolasi pada `StoreCourierRequest` dan `UpdateCourierRequest`, termasuk validasi nomor telepon unik (ignore ID pada update).

---

## Panduan Instalasi & Menjalankan Project

### 1. Clone Repository & Install Dependencies
```bash
git clone <repository-url>
cd laravel-courier-api
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database & Seeding
Database menggunakan SQLite. Buat file database (jika belum ada) lalu jalankan migrasi dan seeder:
```bash
# Windows PowerShell
if (!(Test-Path database/database.sqlite)) { New-Item database/database.sqlite }

# Jalankan migrasi dan seeder
php artisan migrate --seed
```

> **Catatan Seeder:** Seeder otomatis membuat data pengujian spesifik termasuk `"Budiono Hadi Agung"` (Level 3) beserta 24 data kurir lainnya untuk kemudahan pengujian pagination dan pencarian.

### 4. Jalankan Local Server
```bash
php artisan serve
```
API server aktif di: `http://127.0.0.1:8000`

---

## Menjalankan Automated Tests

Test suite ditulis menggunakan **Pest PHP** dan mencakup seluruh skenario fungsionalitas, validasi input, status kode HTTP, serta integritas data di database:

```bash
# Menjalankan seluruh test kurir
php artisan test --filter=CourierApiTest

# Menjalankan seluruh test suite aplikasi
php artisan test
```

### Cakupan Pengujian:
1. `it can list couriers with pagination and default sorting by name ascending`
2. `it can override default sorting to sort by registered_at date`
3. `it matches multi-keyword search such as budi agung to Budiono Hadi Agung`
4. `it can filter couriers by comma-separated levels like 2,3`
5. `it returns all data for a single courier on show endpoint`
6. `it returns 404 when showing non-existent courier`
7. `it validates and stores a courier in database` (memastikan `assertDatabaseHas`)
8. `it fails validation when storing courier with level outside 1-5` (HTTP 422)
9. `it validates and updates an existing courier in database`
10. `it deletes a courier and confirms removal from database` (memastikan `assertDatabaseMissing`)

---

## Dokumentasi API (Endpoints)

Base URL: `http://127.0.0.1:8000/api`

Header default untuk semua request:
```http
Accept: application/json
Content-Type: application/json
```

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/couriers` | List kurir dengan pagination, sorting, search, & filter |
| `POST` | `/couriers` | Tambah kurir baru (Validasi lengkap) |
| `GET` | `/couriers/{id}` | Detail satu data kurir |
| `PUT/PATCH` | `/couriers/{id}` | Update data kurir |
| `DELETE` | `/couriers/{id}` | Hapus kurir dari database |

---

### Query Parameters pada `GET /api/couriers`

| Parameter | Tipe | Contoh | Deskripsi |
| :--- | :--- | :--- | :--- |
| `search` | string | `?search=budi+agung` | Mencari kurir berdasarkan kata kunci nama (multi-kata didukung). |
| `level` | string | `?level=2,3` | Memfilter kurir hanya dengan level tertentu (1–5). |
| `sort_by` | string | `?sort_by=registered_at` | Kolom pengurutan: `name`, `registered_at`, `created_at`, `level`. |
| `order` | string | `?order=desc` | Arah pengurutan: `asc` (default) atau `desc`. |
| `per_page` | integer | `?per_page=10` | Jumlah item per halaman (default 10). |
| `page` | integer | `?page=2` | Nomor halaman pagination. |

---

### Contoh Payload Request & Response

#### 1. `POST /api/couriers` (Store)
**Request Body:**
```json
{
  "name": "Budi Hartono",
  "phone": "081298765432",
  "email": "budi.hartono@example.com",
  "level": 3,
  "is_active": true,
  "registered_at": "2026-09-18 10:00:00"
}
```

**Response (`201 Created`):**
```json
{
  "data": {
    "id": 26,
    "name": "Budi Hartono",
    "phone": "081298765432",
    "email": "budi.hartono@example.com",
    "level": 3,
    "is_active": true,
    "registered_at": "2026-09-18T10:00:00.000000Z",
    "created_at": "2026-09-18T13:40:00.000000Z",
    "updated_at": "2026-09-18T13:40:00.000000Z"
  }
}
```

#### 2. `POST /api/couriers` (Validation Error)
Jika `level` diisi angka selain 1–5 atau nomor `phone` duplikat:

**Response (`422 Unprocessable Content`):**
```json
{
  "message": "The level field must be between 1 and 5.",
  "errors": {
    "level": [
      "The level field must be between 1 and 5."
    ]
  }
}
```

#### 3. `DELETE /api/couriers/{id}` (Destroy)
**Response (`200 OK`):**
```json
{
  "message": "Courier deleted successfully."
}
```

---

## Import Postman / EchoAPI Collection

Repository ini telah dilengkapi file koleksi yang siap di-import:
- File: [`courier-api.postman_collection.json`](./courier-api.postman_collection.json)

**Langkah Import:**
1. Buka Postman, EchoAPI, atau Insomnia.
2. Klik tombol **Import**.
3. Pilih file `courier-api.postman_collection.json` di direktori root repository ini.
4. Semua request (beserta parameter search, sort, filter level, dan contoh JSON body) akan langsung siap digunakan.

---

## Keputusan Arsitektur & Best Practices

- **Form Request Validation**: Validasi dipisahkan dari controller (`StoreCourierRequest` dan `UpdateCourierRequest`) demi mematuhi *Single Responsibility Principle*.
- **API Resource**: Menggunakan `CourierResource` untuk enkapsulasi format representasi JSON dan pemisahan lapisan presentasi dari struktur tabel database.
- **Model Scopes**: Logika pencarian multi-kata (`scopeSearch`) dan filter level (`scopeFilterByLevel`) diisolasi di model `Courier`, menjaga controller tetap bersih dan ringkas.
- **Tokenized Search Implementation**: Query pencarian dipecah per spasi sehingga kata `budi` dan `agung` masing-masing dicocokkan dengan klausa `LIKE '%...%'`, memungkinkan penemuan nama kompleks seperti `"Budiono Hadi Agung"`.
- **Allowed Sort Whitelist**: Mengamankan query pengurutan terhadap potensi error maupun SQL injection dengan memvalidasi nama kolom terhadap daftar whitelist.
