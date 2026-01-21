# 🍽️ Restaurant Management System API

Restaurant Management System API adalah aplikasi backend berbasis REST API yang dibangun menggunakan Laravel 11 untuk mendukung operasional restoran secara menyeluruh, mulai dari manajemen menu, transaksi kasir, hingga koordinasi dapur. Sistem ini menerapkan Role-Based Access Control (RBAC) agar setiap pengguna hanya dapat mengakses fitur sesuai dengan perannya, sehingga keamanan data dan alur kerja tetap terjaga.

Aplikasi ini menggunakan Laravel Sanctum untuk autentikasi berbasis token, MySQL sebagai database utama, serta middleware role custom untuk pengaturan otorisasi pengguna.

## 🚀 Tech Stack
- Laravel 11 (REST API)
- MySQL
- Laravel Sanctum
- Custom Role Middleware (RBAC)

## 🛠️ Fitur
- Autentikasi login berbasis token
- Manajemen menu (CRUD) dengan upload gambar
- Sistem transaksi dan pemesanan
- Perhitungan total harga otomatis di backend
- Manajemen status pesanan (pending, processing, ready, completed)
- Checkout pembayaran oleh kasir

## 👥 Role & Hak Akses
- **Superadmin**: Akses penuh dan manajemen user
- **Admin**: Mengelola menu dan stok
- **Kasir**: Mengelola transaksi, keranjang, dan checkout
- **Chef**: Memantau antrian dapur dan memperbarui status pesanan
- **Owner**: Melihat laporan penjualan (read-only)

## 💻 Instalasi & Menjalankan Aplikasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL (XAMPP / Laragon)

```bash
# Clone repositori
git clone https://github.com/username/cafe-api.git
cd cafe-api

# Install dependensi
composer install

# Konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Migrasi database
php artisan migrate

# Storage link (upload gambar)
php artisan storage:link

# Jalankan server
php artisan serve
```  

Aplikasi akan berjalan di:
http://127.0.0.1:8000
