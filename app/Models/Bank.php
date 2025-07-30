<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'country'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
