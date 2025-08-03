<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSettings extends Model
{
    protected $fillable = [
        'bank_name',
        'payment_term_id',
        'direction',
        'keyword',
    ];
}
