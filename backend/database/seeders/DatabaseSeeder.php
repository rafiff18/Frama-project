<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna (Sesuai Konsep Apotek)
        User::create([
            'name' => 'Superadmin Apotek',
            'email' => 'super@apotek.com',
            'password' => Hash::make('super123'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Apoteker Utama',
            'email' => 'apoteker@apotek.com',
            'password' => Hash::make('apoteker123'),
            'role' => 'apoteker', // Menggantikan peran Admin/Chef
        ]);

        User::create([
            'name' => 'Kasir Apotek',
            'email' => 'kasir@apotek.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        // 2. Data Supplier (suppliers)
        $supplier = Supplier::create([
            'nama_suppliers' => 'PT. Kimia Farma Trading',
            'telepon' => '021-12345678',
            'alamat' => 'Jl. Budi Utomo No. 1, Jakarta',
        ]);

        Supplier::create([
            'nama_suppliers' => 'PT. Enseval Putera Megatrading',
            'telepon' => '021-87654321',
            'alamat' => 'Kawasan Industri Pulogadung, Jakarta',
        ]);

        // 3. Data Master Obat & Alkes
        Obat::create([
            'kode_obat' => 'OBT-001',
            'nama_obat' => 'Paracetamol 500mg',
            'kategori' => 'Obat Bebas',
            'satuan' => 'Strip',
            'stok' => 50,
            'stok_minimal' => 10,
            'harga_beli' => 4500.00,
            'harga_jual' => 6000.00,
            'tgl_kadaluarsa' => '2027-12-31', // Sangat penting untuk Apotek
        ]);

        Obat::create([
            'kode_obat' => 'OBT-002',
            'nama_obat' => 'Amoxicillin 500mg',
            'kategori' => 'Obat Keras',
            'satuan' => 'Strip',
            'stok' => 20,
            'stok_minimal' => 5,
            'harga_beli' => 8000.00,
            'harga_jual' => 12000.00,
            'tgl_kadaluarsa' => '2026-06-15',
        ]);

        Obat::create([
            'kode_obat' => 'ALK-001',
            'nama_obat' => 'Masker Medis 3-Ply',
            'kategori' => 'Alat Kesehatan',
            'satuan' => 'Box',
            'stok' => 100,
            'stok_minimal' => 20,
            'harga_beli' => 25000.00,
            'harga_jual' => 35000.00,
            'tgl_kadaluarsa' => '2029-01-01',
        ]);
    }
}