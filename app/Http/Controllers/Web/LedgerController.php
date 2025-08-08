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

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $transactions = Transaction::with(['owner', 'user'])
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
                            WHEN transactions.owner = ' . $user->id . ' THEN transactions.amount * -1
                            ELSE transactions.amount
                            END as amount'
                )
            )
            ->orderByDesc('created_at')->paginate(20);

        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::where('created_by', $user->id)->get();

        return view('ledger', [
            'transactions' => $transactions,
            'users' => $users,
            'paymentTerms' => $paymentTerms,
            'balance' => $user->balance,
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
                $description .= ' [lent]';
            } else {
                $amount = $amount * -1;
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
            ]);

            if (!$transaction) {
                DB::rollBack();
                return redirect()->back()->withErrors(['message' => 'Transaction could not be created']);
            }

            // update owner's balance
            $owner->balance -= $amount;
            $owner->save();

            // update other user's balance
            $otherUser = User::find($validatedData['user_id']);
            $otherUser->balance += $amount;
            $otherUser->save();

            DB::commit();
            return redirect()->route('ledger')->with('status', 'Transaction was successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
