<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan kolom-kolom berikut diisi
    protected $fillable = [
        'name',
        'price',
        'category',
        'description',
        'image',
        'is_available', // Tambahkan ini jika nanti ingin mengatur ketersediaan menu
    ];
}