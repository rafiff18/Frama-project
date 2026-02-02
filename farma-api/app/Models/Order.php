<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'table_number', 'total_price', 'status'];

    public function details() {
        return $this->hasMany(OrderDetail::class);
    }
}