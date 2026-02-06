<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanDetail extends Model
{
    protected $table = 'penerimaan_details';
    protected $fillable = ['penerimaan_id', 'obat_id', 'qty', 'harga'];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}
