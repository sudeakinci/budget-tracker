<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
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

            $transactions = $query->orderByDesc('created_at')->get();
            $result = $transactions->map(function ($transaction) {
                $ownerUser = User::find($transaction->owner);

                return [
                    'id' => $transaction->id,
                    'owner' => $transaction->owner,
                    'owner_name' => optional($ownerUser)->name ?? 'Unknown',
                    'user_id' => $transaction->user_id,
                    'user_name' => $transaction->user->name ?? null,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'payment_term' => $transaction->payment_term_name,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ];
            });
            return response()->json($result);


        } catch (\Exception $e) {
            return response()->json(['message' => 'No transaction found'], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validatedData = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|not_in:0',
            'description' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'payment_term_name' => 'required_without:payment_term_id|string|max:255',
            'is_sms' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $owner = $user;

            if ($owner->balance < $validatedData['amount']) {
                DB::rollBack();
                return response()->json(['message' => 'Insufficient balance'], 422);
            }

            $paymentTermId = $validatedData['payment_term_id'] ?? null;
            $paymentTermName = $validatedData['payment_term_name'] ?? null;

            if ($paymentTermName) {
                $paymentTerm = PaymentTerm::where('name', $paymentTermName)
                    ->where(function ($query) use ($owner) {
                        $query->where('created_by', $owner->id)
                            ->orWhereNull('created_by');
                    })
                    ->orderByRaw('created_by IS NOT NULL DESC')
                    ->first();

                if (!$paymentTerm) {
                    $paymentTerm = PaymentTerm::create([
                        'name' => $paymentTermName,
                        'created_by' => $owner->id,
                    ]);
                }
                $paymentTermId = $paymentTerm->id;
            } elseif ($paymentTermId) {
                $paymentTerm = PaymentTerm::findOrFail($paymentTermId);
                if ($paymentTerm->created_by !== null && $paymentTerm->created_by !== $owner->id) {
                    DB::rollBack();
                    return response()->json(['message' => 'Forbidden'], 403);
                }
                $paymentTermName = $paymentTerm->name;
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Payment term is required.'], 422);
            }

            $transaction = Transaction::create([
                'owner' => $owner->id,
                'user_id' => $validatedData['user_id'] ?? null,
                'amount' => $validatedData['amount'],
                'description' => $validatedData['description'] ?? null,
                'payment_term_id' => $paymentTermId,
                'payment_term_name' => $paymentTermName,
                'is_sms' => false,
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
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $transaction = Transaction::with(['owner', 'user'])->find($id);

            if (!$transaction || ($transaction->owner != $user->id && $transaction->user_id != $user->id)) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 502);
        }

        return response()->json($transaction);
    }

    public function destroy($id)
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
