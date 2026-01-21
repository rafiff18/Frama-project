<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    protected $fillable = ['name','address','phone'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
