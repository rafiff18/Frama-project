Restaurant Management System (API)
Restaurant Management System adalah aplikasi web berbasis REST API yang dirancang untuk mengelola operasional restoran secara digital, mulai dari manajemen user, menu, transaksi, dapur, hingga laporan. Sistem ini menggunakan arsitektur Role-Based Access Control (RBAC) untuk memastikan keamanan data sesuai dengan tanggung jawab masing-masing staff.

🚀 Tech Stack
Backend: Laravel 11 (REST API).

Frontend: React + Tailwind CSS.

Database: MySQL.

Authentication: Laravel Sanctum.

🛠️ Fitur Utama
Autentikasi & Keamanan: Login sistem menggunakan token Sanctum dan pembatasan akses berdasarkan role staff.

Manajemen Menu: CRUD data menu lengkap dengan fitur upload dan manajemen gambar produk.

Sistem Transaksi: Pembuatan pesanan (order) oleh Kasir dengan perhitungan total harga otomatis di sisi server.

Alur Operasional Dapur: Chef dapat memantau antrian pesanan dan memperbarui status masakan secara real-time.

Manajemen Checkout: Kasir mengelola pembayaran dan penyelesaian transaksi (checkout).

👥 Role & Hak Akses
Setiap akun memiliki batasan akses yang berbeda untuk menjaga integritas data:

Superadmin: Hak penuh terhadap sistem, manajemen user, dan konfigurasi global.

Owner: Monitoring laporan penjualan dan performa menu tanpa akses mengubah data.

Admin: Pengelolaan menu, kategori, dan stok bahan.

Kasir: Membuat transaksi, mengelola keranjang, dan checkout pembayaran.

Chef: Melihat order masuk dan melakukan update status pesanan (diproses/selesai).

💻 Panduan Instalasi & Setup
Ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan projek di lingkungan lokal Anda:

Clone Repositori: Buka terminal dan jalankan perintah untuk menyalin projek:

Bash
git clone https://github.com/rafiff18/Cafe-project.git
cd Cafe-project
Instalasi Dependensi: Instal semua library PHP yang dibutuhkan menggunakan Composer:

Bash
composer install
Konfigurasi Environment: Salin file contoh konfigurasi menjadi file .env aktif:

Bash
cp .env.example .env
Buka file .env dan sesuaikan bagian DB_DATABASE, DB_USERNAME, dan DB_PASSWORD sesuai dengan database MySQL Anda.

Generate Application Key: Buat kunci pengaman aplikasi:

Bash
php artisan key:generate
Migrasi Database: Jalankan migrasi untuk membuat tabel-tabel yang diperlukan di database:

Bash
php artisan migrate
Membuat Symbolic Link Storage: Agar file gambar menu yang diunggah dapat diakses secara publik, jalankan:

Bash
php artisan storage:link
Menjalankan Server: Jalankan server pengembangan Laravel:

Bash
php artisan serve
Aplikasi sekarang dapat diakses melalui URL: http://127.0.0.1:8000
