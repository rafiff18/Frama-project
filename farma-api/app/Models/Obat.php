<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model {
    protected $table = 'obat'; // Paksa pakai nama tabel Indonesia
    protected $fillable = ['kode_obat', 'nama_obat', 'kategori', 'stok', 'harga_beli', 'harga_jual', 'tgl_kadaluarsa'];
}