<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
