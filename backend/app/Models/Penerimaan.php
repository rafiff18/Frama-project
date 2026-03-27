<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $fillable = ['supplier_id', 'user_id', 'no_faktur', 'tgl_penerimaan', 'total_harga'];

    public function details()
    {
        return $this->hasMany(PenerimaanDetail::class, 'penerimaan_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
