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
            ->select(
                'transactions.*',
                DB::raw(
                    'CASE 
                            WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false THEN transactions.amount * -1
                            ELSE transactions.amount
                            END as amount'
                )
            )
            ->orderByDesc('created_at')->paginate(20);



        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::whereNull('created_by')
            ->orWhere('created_by', $user->id)
            ->get();

        return view('transactions', [
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
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'receiver_type' => 'required|in:select,custom',
            'payment_type' => 'required|in:select,custom',
            'user_id' => 'required_if:receiver_type,select|nullable|exists:users,id',
            'custom_user' => 'required_if:receiver_type,custom|nullable|string|max:255',
            'payment_term_id' => 'required_if:payment_type,select|nullable|exists:payment_terms,id',
            'payment_term_name' => 'required_if:payment_type,custom|nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $owner = $user;

            if ($owner->balance < $validatedData['amount']) {
                return redirect()->back()->withErrors(['message' => 'Insufficient balance']);
            }

            $paymentTermId = $validatedData['payment_term_id'] ?? null;
            $paymentTermName = $validatedData['payment_term_name'] ?? null;

            // if payment term is not provided, fetch it from the database
            if (!$paymentTermName) {
                $paymentTerm = PaymentTerm::findOrFail($paymentTermId);
                $paymentTermName = $paymentTerm->name;
            }

            $transaction = Transaction::create([
                'owner' => $owner->id,
                'user_id' => $validatedData['user_id'] ?? null,
                'payment_term_id' => $paymentTermId,
                'payment_term_name' => $paymentTermName,
                'description' => $validatedData['description'] ?? null,
                'amount' => $validatedData['amount'],
            ]);

            if (!$transaction) {
                DB::rollBack();
                return redirect()->back()->withErrors(['message' => 'Transaction could not be created']);
            }

            // update owner's balance
            $owner->balance -= $validatedData['amount'];
            $owner->save();

            // if a receiver user is specified, update their balance
            if (isset($validatedData['user_id'])) {
                $receiver = User::find($validatedData['user_id']);
                $receiver->balance += $validatedData['amount'];
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
            $transaction = Transaction::findOrFail($id);

            if ($transaction->owner !== $user->id) {
                return redirect()->back()->withErrors(['message' => 'You are not authorized to delete this transaction.']);
            }

            if ($transaction->is_sms) {
                return redirect()->back()->withErrors(['message' => 'Transactions created via SMS cannot be deleted.']);
            }

            DB::beginTransaction();

            // restore the balance
            $user->balance += $transaction->amount;
            $user->save();

            // delete the transaction
            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions')->with('status', 'Transaction was successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
