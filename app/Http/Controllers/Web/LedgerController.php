<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['auth'];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user)
            return redirect()->route('login');

        $dateRange = $request->input('date_range');
        $startDate = now()->startOfYear()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

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
            ->where(function ($q) {
                $q->where('description', 'like', '%[lent]%')
                    ->orWhere('description', 'like', '%[borrowed]%');
            })
            ->when($receiverFilter, fn($q) => $q->filterReceiver(explode(',', $receiverFilter)))
            ->orderByDesc('created_at')
            ->paginate(20);

        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::where('created_by', $user->id)->get();

        // stats
        $stats = ['debt' => ['m0' => 0, 'm1' => 0, 'm2' => 0], 'credit' => ['m0' => 0, 'm1' => 0, 'm2' => 0]];

        $transaction = new Transaction();

        $monthlyStats = $transaction->getMonthlyStats($user->id, 3, 'ledger');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthNames = [];
        for ($i = 0; $i < 3; $i++) {
            $date = now()->subMonths($i);
            $monthNames['m' . $i] = $date->format('F');
        }

        foreach ($monthlyStats as $stat) {
            $monthDiff = ($currentYear - $stat->year) * 12 + ($currentMonth - $stat->month);
            if ($monthDiff >= 0 && $monthDiff <= 2) {
                $stats['debt']['m' . $monthDiff] = $stat->expense;
                $stats['credit']['m' . $monthDiff] = $stat->income;
            }
        }

        return view('ledger', compact('transactions', 'users', 'paymentTerms', 'stats', 'monthNames', 'startDate', 'endDate'))
            ->with('balance', $user->balance);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validatedData = $request->validate([
            'amount' => 'required|numeric|not_in:0',
            'description' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'transaction_type' => 'required|in:lent,borrowed',
            'transaction_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $owner = $user;
            $amount = $validatedData['amount'];
            $description = $validatedData['description'] ?? '';

            if ($validatedData['transaction_type'] === 'lent') {
                if ($owner->balance < $amount) {
                    return redirect()->back()->withErrors(['message' => 'Insufficient balance']);
                }
                // when lending, it's an outgoing transaction from the owner (negative)
                $amount = -abs($amount);
                $description .= ' [lent]';
            } else {
                // when borrowing, it's an incoming transaction to the owner (positive)
                $amount = abs($amount);
                $description .= ' [borrowed]';
            }


            $paymentTerm = PaymentTerm::firstOrCreate(['name' => 'N/A'], [
                'created_by' => $owner->id
            ]);

            $transaction = Transaction::create([
                'owner' => $owner->id,
                'user_id' => $validatedData['user_id'],
                'description' => trim($description),
                'amount' => $amount,
                'payment_term_id' => $paymentTerm->id,
                'payment_term_name' => $paymentTerm->name,
                'created_at' => $request->transaction_date ? \Carbon\Carbon::parse($request->transaction_date) : now(),
                'updated_at' => now(),
            ]);

            if (!$transaction) {
                DB::rollBack();
                return redirect()->back()->withErrors(['message' => 'Transaction could not be created']);
            }

            // update owner's balance
            $owner->balance += $amount;
            $owner->save();

            // update other user's balance
            $otherUser = User::find($validatedData['user_id']);
            $otherUser->balance -= $amount;
            $otherUser->save();

            DB::commit();
            return redirect()->route('ledger')->with('status', 'Transaction was successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
