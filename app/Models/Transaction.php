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

    protected $fillable = ['sender_account_id', 'sender_id', 'receiver_account_id', 'receiver_id', 'amount', 'description', 'transaction_type_id', 'is_income', 'date'];

    protected $casts = [
        'is_income' => 'boolean',
        'date' => 'date',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function senderAccount()
    {
        return $this->belongsTo(Account::class, 'sender_account_id');
    }

    public function receiverAccount()
    {
        return $this->belongsTo(Account::class, 'receiver_account_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }
}
