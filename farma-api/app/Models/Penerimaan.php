<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $fillable = ['supplier_id', 'user_id', 'no_faktur', 'tgl_penerimaan', 'total_harga'];

    public function detail()
    {
        return $this->hasMany(PenerimaanDetail::class, 'penerimaan_id');
    }
}
