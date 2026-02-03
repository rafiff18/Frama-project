
# 💊 Dokumen Spesifikasi: ApotekEase

## 1. Deskripsi Sistem

**ApotekEase** adalah aplikasi manajemen apotek berbasis REST API yang dirancang untuk mengotomatisasi operasional harian farmasi. Sistem ini mengintegrasikan manajemen inventaris obat, pelacakan stok real-time, pengadaan barang dari supplier, hingga transaksi penjualan (POS).

**Teknologi Utama:**

* **Backend:** Laravel 11 (REST API) & MySQL
* **Frontend:** Single Page Application (SPA) dengan Tailwind CSS
* **Keamanan:** Role-Based Access Control (RBAC) & Laravel Sanctum

---

## 2. Role & Hak Akses

### 2.1 Superadmin

Memiliki kendali penuh atas ekosistem digital apotek:

* Manajemen akun seluruh staf (Apoteker, Kasir, Owner)
* Audit log aktivitas sistem
* Konfigurasi global (Nama apotek, alamat, nomor izin)
* Reset password dan manajemen database

### 2.2 Owner

Fokus pada aspek bisnis dan pemantauan performa:

* Melihat laporan laba rugi bulanan
* Memantau daftar **Stok Kritis** (obat yang hampir habis)
* Melihat daftar obat yang **Hampir Kadaluarsa** (Expired)
* Hanya akses baca (Read-only) tanpa fitur ubah data

### 2.3 Apoteker (Pengganti Chef/Admin)

Bertanggung jawab atas validitas medis dan stok barang:

* CRUD Data Master Obat (Kode, Kategori, Satuan)
* Manajemen data Supplier/Pemasok
* Input **Penerimaan Barang** (menambah stok otomatis)
* Verifikasi tanggal kadaluarsa obat

### 2.4 Kasir

Bertanggung jawab pada layanan transaksi langsung:

* Membuat transaksi penjualan obat ke pasien
* Input keranjang belanja dan perhitungan total otomatis
* Checkout pembayaran dan cetak struk

---

## 3. Alur Sistem

### 3.1 Alur Login

User memasukkan kredensial, sistem memvalidasi melalui Sanctum, dan mengembalikan token serta role untuk mengatur tampilan navigasi di frontend.

### 3.2 Alur Barang Masuk (Procurement)

1. **Apoteker** memilih supplier dan menginput daftar obat yang datang.
2. Sistem menambah jumlah **Stok** di tabel obat secara otomatis.
3. Riwayat penerimaan tersimpan untuk audit stok.

### 3.3 Alur Penjualan (POS)

1. **Kasir** memilih obat yang diminta pasien.
2. Sistem mengecek ketersediaan stok; jika cukup, pesanan diproses.
3. Setelah checkout, **Stok Berkurang** secara otomatis dan transaksi tercatat.

---

## 4. Arsitektur & Target Sistem

* **Keamanan:** Autentikasi token yang kedaluwarsa secara berkala.
* **Integritas:** Perhitungan harga beli vs harga jual untuk laporan margin keuntungan yang akurat.
* **Skalabilitas:** Mudah ditambah fitur integrasi BPJS atau Resep Digital di masa depan.

---

## 5. Kredensial Akun Default

| **Role**       | **Email**         | **Password** | **Akses Utama**  |
| -------------------- | ----------------------- | ------------------ | ---------------------- |
| **Superadmin** | `super@apotek.com`    | `super123`       | Kelola User & Sistem   |
| **Apoteker**   | `apoteker@apotek.com` | `obat123`        | Kelola Stok & Supplier |
| **Kasir**      | `kasir@apotek.com`    | `kasir123`       | Transaksi & Penjualan  |
| **Owner**      | `owner@apotek.com`    | `owner123`       | Laporan & Monitoring   |
