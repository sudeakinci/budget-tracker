<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Bank extends Model
{
    use HasFactory;
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
