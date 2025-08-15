<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Models\Transaction;
use \App\Models\User;
use \App\Models\PaymentTerm;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['auth'];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $amountType = $request->input('amount_type', 'all');
        $receiverFilter = $request->input('receiver');

        // get the latest 5 transactions for the user
        $transactionsQuery = Transaction::with(['owner', 'user'])
            ->where(function ($q) use ($user) {
                $q->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('transactions.*') // Select all transaction columns
            ->addSelect(DB::raw(
                'CASE 
                       WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false 
                       THEN transactions.amount * -1
                       ELSE transactions.amount
                       END as display_amount'
            ));

        // Apply amount type filter (income/expense)
        if ($amountType !== 'all') {
            $transactionsQuery->where(function ($q) use ($user, $amountType) {
                if ($amountType === 'income') {
                    // (owner is user AND not SMS AND amount > 0) OR (owner is not user OR is SMS AND amount < 0)
                    $q->where(function ($sub) use ($user) {
                        $sub->where('transactions.owner', $user->id)
                            ->where('transactions.is_sms', false)
                            ->where('transactions.amount', '>', 0);
                    })->orWhere(function ($sub) use ($user) {
                        $sub->where(function ($sub2) use ($user) {
                            $sub2->where('transactions.owner', '!=', $user->id)
                                ->orWhere('transactions.is_sms', true);
                        })->where('transactions.amount', '<', 0);
                    });
                } elseif ($amountType === 'expense') {
                    // (owner is user AND not SMS AND amount < 0) OR (owner is not user OR is SMS AND amount > 0)
                    $q->where(function ($sub) use ($user) {
                        $sub->where('transactions.owner', $user->id)
                            ->where('transactions.is_sms', false)
                            ->where('transactions.amount', '<', 0);
                    })->orWhere(function ($sub) use ($user) {
                        $sub->where(function ($sub2) use ($user) {
                            $sub2->where('transactions.owner', '!=', $user->id)
                                ->orWhere('transactions.is_sms', true);
                        })->where('transactions.amount', '>', 0);
                    });
                }
            });
        }

        // Apply receiver filter if provided
        if ($receiverFilter) {
            $receivers = explode(',', $receiverFilter);
            $transactionsQuery->where(function($q) use ($receivers) {
                $q->whereHas('user', function($userQuery) use ($receivers) {
                    $userQuery->whereIn('name', $receivers);
                })
                ->orWhereIn('receiver', $receivers);
            });
        }


        $transactions = $transactionsQuery->orderByDesc('created_at')
            ->take(5)
            ->get();

        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::whereNull('created_by')
            ->orWhere('created_by', $user->id)
            ->get();


        return view('dashboard', compact('users', 'transactions', 'paymentTerms'));
    }
}
