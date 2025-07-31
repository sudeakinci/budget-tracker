<?php

namespace App\Models;

use App\Models\User;
use App\Models\Account;
use App\Models\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['owner', 'user_id', 'payment_term', 'description', 'amount'];

    protected $casts = [];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
