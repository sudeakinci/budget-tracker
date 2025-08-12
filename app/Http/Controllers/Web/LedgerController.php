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

        if (!$user) {
            return redirect()->route('login');
        }

        $amountType = $request->input('amount_type', 'all');
        $receiverFilter = $request->input('receiver');

        $query = Transaction::with(['owner', 'user'])
            ->where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->where(function ($query) {
                $query->where('description', 'like', '%[lent]%')
                    ->orWhere('description', 'like', '%[borrowed]%');
            })
            ->select(
                'transactions.*',
                DB::raw(
                    'CASE 
                            WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false
                            THEN transactions.amount * -1
                            ELSE transactions.amount
                            END as display_amount'
                )
            );
            
        // Apply amount type filter (income/expense)
        if ($amountType !== 'all') {
            if ($amountType === 'income') {
                $query->where(DB::raw(
                    'CASE 
                        WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false 
                        THEN transactions.amount * -1
                        ELSE transactions.amount
                        END'
                ), '<', 0); // Income is negative in display_amount
            } elseif ($amountType === 'expense') {
                $query->where(DB::raw(
                    'CASE 
                        WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false 
                        THEN transactions.amount * -1
                        ELSE transactions.amount
                        END'
                ), '>', 0); // Expense is positive in display_amount
            }
        }
        
        // Apply receiver filter if provided
        if ($receiverFilter) {
            $receivers = explode(',', $receiverFilter);
            $query->whereHas('user', function($q) use ($receivers) {
                $q->whereIn('name', $receivers);
            });
        }
            
        $transactions = $query->orderByDesc('created_at')->paginate(20);

        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::where('created_by', $user->id)->get();
        
        // calculate monthly statistics for last 3 months
        $stats = [
            'debt' => ['m0' => 0, 'm1' => 0, 'm2' => 0],
            'credit' => ['m0' => 0, 'm1' => 0, 'm2' => 0]
        ];
        
        // get statistics for the last 3 months
        $monthlyStats = Transaction::where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->where(function ($query) {
                $query->where('description', 'like', '%[lent]%')
                    ->orWhere('description', 'like', '%[borrowed]%');
            })
            ->where('created_at', '>=', now()->subMonths(3))
            ->select(
                DB::raw(config('database.default') === 'sqlite' 
                    ? "CAST(strftime('%m', created_at) AS INTEGER) as month" 
                    : 'MONTH(created_at) as month'),
                DB::raw(config('database.default') === 'sqlite' 
                    ? "CAST(strftime('%Y', created_at) AS INTEGER) as year" 
                    : 'YEAR(created_at) as year'),
                DB::raw('SUM(CASE 
                    WHEN (owner = ' . $user->id . ' AND amount > 0) OR (user_id = ' . $user->id . ' AND amount < 0) 
                    THEN ABS(amount) 
                    ELSE 0 
                    END) as credit'),
                DB::raw('SUM(CASE 
                    WHEN (owner = ' . $user->id . ' AND amount < 0) OR (user_id = ' . $user->id . ' AND amount > 0) 
                    THEN ABS(amount) 
                    ELSE 0 
                    END) as debt')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // map the statistics to the stats array
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // generate month names for the last 3 months
        $monthNames = [];
        for ($i = 0; $i < 3; $i++) {
            $date = now()->subMonths($i);
            $monthNames['m' . $i] = $date->format('F'); // full month name (e.g., "August")
        }
        
        foreach ($monthlyStats as $index => $stat) {
            $monthDiff = ($currentYear - $stat->year) * 12 + ($currentMonth - $stat->month);
            
            if ($monthDiff >= 0 && $monthDiff <= 2) {
                $stats['debt']['m' . $monthDiff] = $stat->debt;
                $stats['credit']['m' . $monthDiff] = $stat->credit;
            }
        }
        

        return view('ledger', [
            'transactions' => $transactions,
            'users' => $users,
            'paymentTerms' => $paymentTerms,
            'balance' => $user->balance,
            'stats' => $stats,
            'monthNames' => $monthNames,
        ]);
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
