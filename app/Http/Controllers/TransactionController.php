<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $query = Transaction::with(['owner', 'user'])
                ->where(function ($q) use ($user) {
                    $q->where('owner', $user->id)
                        ->orWhere('user_id', $user->id);
                })
                ->select(
                    'transactions.*',
                    DB::raw(
                        'CASE 
                            WHEN transactions.owner = ' . $user->id . ' THEN transactions.amount * -1
                            ELSE transactions.amount
                            END as amount'
                    )
                );
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
            }

            $result = $query->latest()->paginate(10);
            return response()->json($result);


        } catch (\Exception $e) {
            return response()->json(['message' => 'No transaction found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'owner' => 'required|exists:users,id',
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'payment_term_name' => 'required_without:payment_term_id|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $owner = User::find($validatedData['owner']);

            if ($owner->balance < $validatedData['amount']) {
                DB::rollBack();
                return response()->json(['message' => 'Insufficient balance'], 422);
            }

            $paymentTermId = $validatedData['payment_term_id'] ?? null;
            $paymentTermName = $validatedData['payment_term_name'] ?? null;

            if ($paymentTermName) {
                $paymentTerm = PaymentTerm::where('name', $paymentTermName)
                    ->where(function ($query) use ($owner) {
                        $query->where('user_id', $owner->id)
                            ->orWhereNull('user_id');
                    })
                    ->orderByRaw('user_id IS NOT NULL DESC')
                    ->first();

                if (!$paymentTerm) {
                    $paymentTerm = PaymentTerm::create([
                        'name' => $paymentTermName,
                        'user_id' => $owner->id,
                    ]);
                }
                $paymentTermId = $paymentTerm->id;
            } else {
                $paymentTerm = PaymentTerm::findOrFail($paymentTermId);
                if ($paymentTerm->user_id !== null && $paymentTerm->user_id !== $owner->id) {
                    DB::rollBack();
                    return response()->json(['message' => 'Forbidden'], 403);
                }
                $paymentTermName = $paymentTerm->name;
            }

            $transaction = Transaction::create([
                'owner' => $validatedData['owner'],
                'user_id' => $validatedData['user_id'] ?? null,
                'amount' => $validatedData['amount'],
                'description' => $validatedData['description'],
                'payment_term_id' => $paymentTermId,
                'payment_term_name' => $paymentTermName,
            ]);

            if (!$transaction) {
                DB::rollBack();
                return response()->json(['message' => 'Transaction not created'], 500);
            }

            $owner->balance -= $validatedData['amount'];
            $owner->save();

            if (isset($validatedData['user_id'])) {
                $user = User::find($validatedData['user_id']);
                $user->balance += $validatedData['amount'];
                $user->save();
            }

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An unexpected error occurred.', 'error' => $e->getMessage()], 502);
        }
    }
    public function show($id)
    {
        try {
            $transaction = Transaction::with(['owner', 'user'])->find($id);

            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 502);
        }

        return response()->json($transaction);
    }

    public function destroy($id, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // only owner can delete the transaction
        if ($transaction->owner != $user->id) {
            return response()->json(['message' => 'You are not authorized to delete this transaction.'], 403);
        }

        try {
            DB::beginTransaction();

            $owner = User::find($transaction->owner);
            $user = User::find($transaction->user_id);

            $owner->balance += $transaction->amount;
            $owner->save();

            if ($user) {
                $user->balance -= $transaction->amount;
                $user->save();
            }

            $transaction->delete();

            DB::commit();

            return response()->json(['message' => 'Transaction deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 502);
        }
    }
}
