<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
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

        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $amountType = $request->input('amount_type', 'all');
        $receiverFilter = $request->input('receiver');

        $query = Transaction::with(['owner', 'user'])
            ->where(function ($q) use ($user) {
                $q->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
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
        
        // Get all transactions for the display list, including non-included ones
        $transactions = $query->orderByDesc('created_at')->paginate(20);

        // calculate monthly statistics for last 3 months
        $stats = [
            'income' => ['m0' => 0, 'm1' => 0, 'm2' => 0],
            'expense' => ['m0' => 0, 'm1' => 0, 'm2' => 0]
        ];
        
        // get statistics for the last 3 months (only included transactions)
        $monthlyStats = Transaction::where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->where('is_included', true)
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
                    END) as income'),
                DB::raw('SUM(CASE 
                    WHEN (owner = ' . $user->id . ' AND amount < 0) OR (user_id = ' . $user->id . ' AND amount > 0)
                    THEN ABS(amount) 
                    ELSE 0 
                    END) as expense')
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
                $stats['expense']['m' . $monthDiff] = $stat->expense;
                $stats['income']['m' . $monthDiff] = $stat->income;
            }
        }

        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::whereNull('created_by')
            ->orWhere('created_by', $user->id)
            ->get();

        return view('transactions', [
            'transactions' => $transactions,
            'users' => $users,
            'paymentTerms' => $paymentTerms,
            'balance' => $user->balance,
            'stats' => $stats,
            'monthNames' => $monthNames,
            'startDate' => $startDate,
            'endDate' => $endDate,
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
            'receiver_type' => 'required|in:select,custom',
            'payment_type' => 'required|in:select,custom',
            'user_id' => 'nullable|exists:users,id',
            'payment_term_id' => 'required_if:payment_type,select|nullable|exists:payment_terms,id',
            'payment_term_name' => 'required_if:payment_type,custom|nullable|string|max:255',
            'transaction_type' => 'required|in:income,expense',
            'is_included' => 'nullable|boolean',
            'transaction_date' => 'nullable|date',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $owner = $user;
            $amount = $validatedData['amount'];

            // check if the owner has sufficient balance and if the transaction type is expense
            if ($validatedData['transaction_type'] === 'expense') {
                if ($owner->balance < $amount) {
                    return redirect()->back()->withErrors(['message' => 'Insufficient balance']);
                }
                $amount = -abs($amount); // Convert to negative for expense
            } else {
                $amount = abs($amount);
            }

            $paymentTermId = $validatedData['payment_term_id'] ?? null;
            $paymentTermName = $validatedData['payment_term_name'] ?? null;

            // If custom payment term is provided, create a new payment term record
            if ($validatedData['payment_type'] === 'custom' && $paymentTermName) {
                $paymentTerm = PaymentTerm::create([
                    'name' => $paymentTermName,
                    'created_by' => $owner->id
                ]);
                $paymentTermId = $paymentTerm->id;
            }
            // If payment term is selected but name is not provided, fetch it from the database
            elseif (!$paymentTermName && $paymentTermId) {
                $paymentTerm = PaymentTerm::findOrFail($paymentTermId);
                $paymentTermName = $paymentTerm->name;
            }

            $transaction = Transaction::create([
                'owner' => $owner->id,
                'user_id' => $validatedData['user_id'] ?? null,
                'payment_term_id' => $paymentTermId,
                'payment_term_name' => $paymentTermName,
                'description' => $validatedData['description'] ?? null,
                'amount' => $amount,
                'is_included' => $request->has('is_included') ? true : false,
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

            // if a receiver user is specified, update their balance
            if (isset($validatedData['user_id'])) {
                $receiver = User::find($validatedData['user_id']);
                $receiver->balance -= $amount;
                $receiver->save();
            }

            DB::commit();
            return redirect()->route('transactions')->with('status', 'Transaction was successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $transaction = Transaction::with(['owner', 'user'])
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->first();

        if (!$transaction) {
            return redirect()->back()->withErrors(['message' => 'Transaction not found']);
        }

        return view('transaction_show', ['transaction' => $transaction]);
    }

    public function update(Request $request, Transaction $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        //check if the user is authorized to update this transaction
        if ($id->is_sms || $id->owner != $user->id) {
            return redirect()->back()->withErrors(['message' => 'You are not authorized to update this transaction.']);
        }

        $validatedData = $request->validate([
            'description' => 'nullable|string|max:255',
            'payment_type' => 'required|in:select,custom',
            'payment_term_id' => 'required_if:payment_type,select|nullable|exists:payment_terms,id',
            'payment_term_name' => 'required_if:payment_type,custom|nullable|string|max:255',
            'is_included' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $paymentTermId = $validatedData['payment_type'] === 'select' ? $validatedData['payment_term_id'] : null;
            $paymentTermName = $validatedData['payment_type'] === 'custom' ? $validatedData['payment_term_name'] : null;

            // if payment term is not provided, fetch it from the database
            if (!$paymentTermName && $paymentTermId) {
                $paymentTerm = PaymentTerm::findOrFail($paymentTermId);
                $paymentTermName = $paymentTerm->name;
            }

            // update the transaction
            $id->description = $validatedData['description'];
            $id->payment_term_id = $paymentTermId;
            $id->payment_term_name = $paymentTermName;
            $id->is_included = $request->has('is_included') ? true : false;
            $id->save();

            DB::commit();
            return redirect()->back()->with('status', 'Transaction was successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $transaction = Transaction::with('user')->findOrFail($id);

            if ($transaction->owner !== $user->id) {
                return redirect()->back()->withErrors(['message' => 'You are not authorized to delete this transaction.']);
            }

            if ($transaction->is_sms) {
                return redirect()->back()->withErrors(['message' => 'Transactions created via SMS cannot be deleted.']);
            }

            DB::beginTransaction();

            // Restore the owner's balance
            $user->balance -= $transaction->amount; // Subtract because we're reversing the transaction
            $user->save();

            // If there's a recipient user, update their balance too
            if ($transaction->user_id) {
                $receiver = User::find($transaction->user_id);
                if ($receiver) {
                    $receiver->balance += $transaction->amount; // Add because we're reversing the transaction for them
                    $receiver->save();
                }
            }

            // Delete the transaction
            $transaction->delete();

            DB::commit();
            
            // Redirect with flash message that will trigger JS to refresh the stats
            return redirect()->route('transactions')->with('status', 'Transaction was successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    public function updateInclusion(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            
            // Check if the user is authorized to update this transaction
            $user = Auth::user();
            if ($transaction->owner != $user->id && $transaction->user_id != $user->id) {
                return response()->json(['error' => 'Unauthorized action'], 403);
            }
            
            // Update the inclusion status
            $transaction->is_included = $request->input('is_included', false);
            $transaction->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction inclusion status updated successfully',
                'is_included' => $transaction->is_included
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
