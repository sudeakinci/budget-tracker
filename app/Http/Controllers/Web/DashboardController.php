<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['auth'];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $dateRange = $request->input('date_range');
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        if ($dateRange) {
            $dates = explode(' to ', $dateRange);
            $startDate = $dates[0];
            $endDate = $dates[1] ?? $dates[0];
        }

        $amountType = $request->input('amount_type', 'all');
        $receiverFilter = $request->input('receiver');

        $transactions = Transaction::with(['owner', 'user'])
            ->forUser($user->id)
            ->dateRange($startDate, $endDate)
            ->withDisplayAmount($user->id)
            ->filterAmountType($user->id, $amountType)
            ->when($receiverFilter, fn($q) => $q->filterReceiver(explode(',', $receiverFilter)))
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
