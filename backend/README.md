# MediCare AI - Apotik Management System

Sistem Informasi Manajemen Apotik (Point of Sale & Inventory) modern yang dibangun menggunakan:
- **Backend:** Laravel 12 & MySQL/SQLite
- **Frontend:** Vue.js 3, Vite, & Tailwind CSS
- **Authentication:** Laravel Sanctum

## Fitur Utama
1. **Dashboard & Analitik:** Ringkasan data (Total Obat, Stok Menipis, Transaksi Hari Ini, Pendapatan).
2. **Kasir (POS - Point of Sale):** Sistem kasir interaktif dengan indikator stok (Aman/Menipis/Habis) dan kalkulasi otomatis. Dukungan Dark Mode yang nyaman.
3. **Manajemen Inventori:** Kelola dafar obat, kategori, harga modal (beli), dan harga jual.
4. **Penerimaan Stok (Restock):** Sistem cerdas (Smart Pricing). Saat menerima restock dengan harga modal baru yang lebih tinggi, sistem otomatis menyesuaikan Harga Jual dengan Margin 20% (dibulatkan ke kelipatan 500 IDR).
5. **Manajemen Supplier:** Kelola data pemasok.
6. **Laporan Keuangan:** Rekapitulasi riwayat transaksi penjualan.
7. **Kelola Staf/Pengguna:** Hak akses multi-role (Superadmin, Kasir, dll).

## Persyaratan Sistem (Requirements)
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / MariaDB (Atau SQLite untuk testing)

## Panduan Instalasi (Development)

1. **Clone & Masuk Direktori**
   ```bash
   git clone <repo_url>
   cd farma-api
   ```

2. **Install Dependensi PHP & Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   - Copy file `.env.example` menjadi `.env`.
   - Buka file `.env` dan atur koneksi database Anda (misalnya ubah `DB_CONNECTION=mysql`, `DB_DATABASE=nama_db`).
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seeder Database (Opsional)**
   Menyiapkan tabel dan data dummy awal:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   Anda membutuhkan dua terminal / command prompt:
   
   **Terminal 1 (Backend - Laravel):**
   ```bash
   php artisan serve
   ```
   
   **Terminal 2 (Frontend - Vite/Vue):**
   ```bash
   npm run dev
   ```

   Akses aplikasi melalui browser: `http://localhost:8000`.

## Pengujian (Testing)
Proyek ini menggunakan PHPUnit untuk automated testing, khususnya untuk *Smart Pricing Logic*.
```bash
php artisan test
```

## Mode Gelap (Dark Mode)
Aplikasi sudah terintegrasi penuh dengan sistem *Global Dark Mode* yang dapat di toggle secara langsung di pojok kanan atas layar via ikon Bulan/Matahari. Pengaturan ini akan tersimpan permanen di browser (localStorage).

---
*Dikembangkan menggunakan teknologi modern untuk memudahkan operasional bisnis apotek yang cepat, presisi, dan pintar.*
