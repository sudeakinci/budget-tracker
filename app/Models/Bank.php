<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'branch', 'account_number', 'iban'];
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
