# Restaurant Management System

## 1. Deskripsi Sistem

Restaurant Management System adalah aplikasi web berbasis REST API untuk membantu operasional restoran mulai dari manajemen user, menu, transaksi, dapur, hingga laporan.

Sistem ini menggunakan:

- Backend: Laravel (API) + MySQL (XAMPP)
- Frontend: React + Tailwind
- Role-based access control

---

## 2. Role & Hak Akses

### 2.1 Superadmin

Hak penuh terhadap sistem:

- CRUD semua user (owner, admin, kasir, chef)
- CRUD restoran
- Akses seluruh data & laporan
- Reset password user
- Konfigurasi sistem global

### 2.2 Owner

Monitoring & evaluasi:

- Melihat laporan penjualan
- Melihat performa menu
- Melihat aktivitas transaksi
- Tidak bisa mengubah data
- Tidak bisa kelola user

### 2.3 Admin

Operasional terbatas:

- CRUD menu
- CRUD kategori menu
- Kelola stok bahan
- Tidak bisa melihat laporan keuangan penuh
- Tidak bisa mengelola user

### 2.4 Kasir

Operasional transaksi:

- Membuat transaksi
- Mengelola keranjang
- Checkout pembayaran
- Cetak struk

### 2.5 Chef

Operasional dapur:

- Melihat order masuk
- Update status pesanan (diproses / selesai)
- Tidak bisa mengakses transaksi & laporan

---

## 3. Alur Sistem

### 3.1 Alur Login

1. User login
2. Sistem validasi
3. Token + role dikirim
4. Frontend redirect sesuai role

### 3.2 Alur Transaksi

1. Kasir memilih menu
2. Sistem membuat order
3. Order masuk ke chef
4. Chef memproses pesanan
5. Status order diperbarui
6. Transaksi selesai

### 3.3 Alur Laporan

1. Owner membuka dashboard
2. Sistem mengambil data transaksi
3. Ditampilkan dalam bentuk ringkasan & grafik

---

## 4. Arsitektur Sistem

- Backend: Laravel REST API
- Frontend: SPA (React)
- Auth: Laravel Sanctum
- Authorization: Middleware role
- Komunikasi: JSON

---

## 5. Target Sistem

- Aman (role-based)
- Mudah dikembangkan
- Cocok untuk UMKM restoran
- Skalabel untuk multi restoran

---

## 6. Pengembangan Selanjutnya (Opsional)

- Multi cabang
- Barcode menu
- Dashboard real-time
- Export laporan PDF


| **Role**       | **Email**    | **Password** | **Hak Akses Utama**  |
| -------------------- | ------------------ | ------------------ | -------------------------- |
| **Superadmin** | `super@cafe.com` | `super123`       | Semua fitur + Kelola User. |
| **Admin**      | `admin@cafe.com` | `admin123`       | Kelola Menu & Kategori.    |
| **Chef**       | `chef@cafe.com`  | `chef123`        | Update Status Pesanan.     |
| **Kasir**      | `kasir@cafe.com` | `kasir123`       | Buat Order & Checkout.     |
| **Owner**      | `owner@cafe.com` | `owner123`       | Monitoring Laporan.        |
