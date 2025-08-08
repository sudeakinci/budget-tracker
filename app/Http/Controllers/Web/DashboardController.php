<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Models\Transaction;
use \App\Models\User;
use \App\Models\PaymentTerm;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['auth'];
    }

    public function index()
    {
        $user = Auth::user();

        // get the latest 5 transactions for the user
        $transactions = Transaction::with(['owner', 'user'])
            ->where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->select(
                'transactions.*',
                DB::raw(
                    'CASE 
                        WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false THEN transactions.amount * -1
                        ELSE transactions.amount
                        END as amount'
                )
            )
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::whereNull('created_by')
            ->orWhere('created_by', $user->id)
            ->get();


        return view('dashboard', compact('users', 'transactions', 'paymentTerms'));
    }
}
