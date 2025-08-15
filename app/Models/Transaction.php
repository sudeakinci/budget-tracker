<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['owner', 'user_id', 'payment_term_id', 'payment_term_name', 'description', 'amount', 'is_sms', 'is_included', 'created_at', 'updated_at', 'receiver'];

    protected $casts = [
        'amount' => 'float',
        'is_sms' => 'boolean',
        'is_included' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }
}
