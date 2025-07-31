<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'country'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
