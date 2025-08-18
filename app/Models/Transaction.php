<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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


    // ===== SCOPES =====

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('owner', $userId)
                ->orWhere('user_id', $userId);
        });
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
    }

    public function scopeWithDisplayAmount($query, $userId)
    {
        return $query->select('transactions.*')
            ->addSelect(DB::raw(
                'CASE
                WHEN transactions.owner = ' . $userId . ' AND transactions.is_sms = false
                THEN transactions.amount * -1
                ELSE transactions.amount
            END as display_amount'
            ));
    }

    public function scopeFilterAmountType($query, $userId, $amountType)
    {
        if ($amountType === 'income') {
            return $query->where(DB::raw("CASE 
                WHEN transactions.owner = {$userId} AND transactions.is_sms = false 
                THEN transactions.amount * -1
                ELSE transactions.amount
            END"), '<', 0);
        } elseif ($amountType === 'expense') {
            return $query->where(DB::raw("CASE 
                WHEN transactions.owner = {$userId} AND transactions.is_sms = false 
                THEN transactions.amount * -1
                ELSE transactions.amount
            END"), '>', 0);
        }
        return $query;
    }

    public function scopeFilterReceiver($query, $receivers)
    {
        return $query->where(function ($q) use ($receivers) {
            $q->whereHas('user', function ($userQuery) use ($receivers) {
                $userQuery->whereIn('name', $receivers);
            })->orWhereIn('receiver', $receivers);
        });

    }


    // ===== HELPER FUNCTIONS =====

    public function getMonthlyStats($userId, $months, $mode = 'transactions')
    {
        $query = self::where(function ($q) use ($userId) {
            $q->where('owner', $userId)->orWhere('user_id', $userId);
        });

        // for ledger filtering
        if ($mode === 'ledger') {
            $query->where(function ($q) {
                $q->where('description', 'like', '%[lent]%')
                    ->orWhere('description', 'like', '%[borrowed]%');
            });
        } else {
            $query->where('is_included', true);
        }

        return $query->where('created_at', '>=', now()->subMonths($months))
            ->select(
                DB::raw(config('database.default') === 'sqlite'
                    ? "CAST(strftime('%m', created_at) AS INTEGER) as month"
                    : "MONTH(created_at) as month"),
                DB::raw(config('database.default') === 'sqlite'
                    ? "CAST(strftime('%Y', created_at) AS INTEGER) as year"
                    : "YEAR(created_at) as year"),
                DB::raw('SUM(CASE 
                    WHEN (owner = ' . $userId . ' AND amount > 0) OR (user_id = ' . $userId . ' AND amount < 0) 
                    THEN ABS(amount) 
                    ELSE 0 END) as income'),
                DB::raw('SUM(CASE 
                    WHEN (owner = ' . $userId . ' AND amount < 0) OR (user_id = ' . $userId . ' AND amount > 0) 
                    THEN ABS(amount) 
                    ELSE 0 END) as expense')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }
}
