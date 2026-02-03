
# 💊 ApotekEase API

**ApotekEase API** adalah solusi *backend* tangguh berbasis REST API yang dibangun dengan  **Laravel 11** . Dirancang khusus untuk mendigitalisasi operasional apotek, mulai dari manajemen inventaris obat yang ketat, pelacakan tanggal kadaluarsa, hingga sistem kasir (POS) yang terintegrasi.

Dengan sistem  **Role-Based Access Control (RBAC)** , aplikasi ini memastikan setiap staf—baik Apoteker, Kasir, maupun Owner—bekerja dalam koridor akses yang aman dan efisien.

---

## 🛠️ Tech Stack

* **Framework:** Laravel 11 (REST API)
* **Database:** MySQL
* **Security:** Laravel Sanctum (Token-based Auth)
* **Architecture:** Layered Controller dengan Custom Middleware Role

---

## 🌟 Fitur Unggulan

* 🔐 **Autentikasi Aman:** Sistem login berbasis token menggunakan Laravel Sanctum.
* 📦 **Inventaris Obat (Smart Inventory):** CRUD obat lengkap dengan kategori, satuan, dan pengingat stok minimal.
* ⏳ **Expiry Tracking:** Pelacakan otomatis tanggal kadaluarsa untuk menjamin keamanan pasien.
* 📥 **Penerimaan Barang:** Logika otomatis penambahan stok saat barang datang dari supplier.
* 🛒 **Sistem Kasir (POS):** Pengurangan stok *real-time* saat transaksi penjualan berhasil.
* 📊 **Laporan Laba Rugi:** Monitoring keuangan yang transparan bagi Owner.

---

## 👥 Struktur Role & Hak Akses

| **Role**       | **Deskripsi Akses**                                      |
| -------------------- | -------------------------------------------------------------- |
| **Superadmin** | Kontrol penuh sistem, manajemen user/staff, dan audit data.    |
| **Apoteker**   | Mengelola data obat, supplier, dan memproses barang masuk.     |
| **Kasir**      | Fokus pada transaksi penjualan dan cetak struk.                |
| **Owner**      | Monitoring stok kritis, obat kadaluarsa, dan laporan keuangan. |

---

## 🚀 Instalasi Cepat

### Prasyarat

* PHP >= 8.2
* Composer & MySQL

### Langkah-langkah

**Bash**

```
# Clone project
git clone https://github.com/username/farma-api.git
cd farma-api

# Install dependencies
composer install

# Setup Environment
cp .env.example .env
php artisan key:generate

# Database & Seed (Menyiapkan tabel & akun awal)
php artisan migrate:fresh --seed

# Jalankan Server
php artisan serve
```

---

## 📡 API Endpoints (Preview)

| **Method** | **Endpoint**  | **Fungsi**                 |
| ---------------- | ------------------- | -------------------------------- |
| `POST`         | `/api/login`      | Mendapatkan Token Akses          |
| `GET`          | `/api/obats`      | Melihat Daftar Stok Obat         |
| `POST`         | `/api/penerimaan` | Menambah Stok (Apoteker Only)    |
| `POST`         | `/api/penjualan`  | Transaksi Kasir (Stok Berkurang) |

---

## 🤝 Kontribusi

Aplikasi ini dikembangkan untuk mempermudah tata kelola obat di Indonesia. Silakan ajukan *pull request* jika ingin menambahkan fitur seperti integrasi BPJS atau resep digital.
