<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['sender', 'receiver', 'senderAccount', 'receiverAccount', 'transactionType']);

        if ($request->has('is_income')) {
            $query->where('is_income', filter_var($request->input('is_income'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
        }

        return $query->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'sender_account_id' => 'required|exists:accounts,id',
                'receiver_account_id' => 'required|exists:accounts,id', // |different:sender_account_id
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string',
                'transaction_type_id' => 'required|exists:transaction_types,id',
                'date' => 'required|date',
            ]);

            $senderAccount = Account::find($validatedData['sender_account_id']);
            $receiverAccount = Account::find($validatedData['receiver_account_id']);

            if ($senderAccount->balance < $validatedData['amount']) {
                return response()->json([
                    'message' => 'Insufficient balance',
                    'errors' => ['amount' => ['Sender account balance is insufficient']]
                ], 422);
            }

            DB::beginTransaction();

            $senderAccount->decrement('balance', $validatedData['amount']);
            $receiverAccount->increment('balance', $validatedData['amount']);

            $transaction = Transaction::create([
                'sender_account_id' => $validatedData['sender_account_id'],
                'sender_id' => $senderAccount->user_id,
                'receiver_account_id' => $validatedData['receiver_account_id'],
                'receiver_id' => $receiverAccount->user_id,
                'amount' => $validatedData['amount'],
                'description' => $validatedData['description'],
                'transaction_type_id' => $validatedData['transaction_type_id'],
                'is_income' => false,
                'date' => $validatedData['date'],
            ]);

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with(['sender', 'receiver', 'senderAccount', 'receiverAccount', 'transactionType'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        try {
            DB::beginTransaction();

            $senderAccount = Account::find($transaction->sender_account_id);
            $receiverAccount = Account::find($transaction->receiver_account_id);

            if ($transaction->is_income) {
                // if income transaction, revert decrementing receiver and incrementing sender
                $receiverAccount->decrement('balance', $transaction->amount);
                $senderAccount->increment('balance', $transaction->amount);
            } else {
                // if outcome transaction, revert incrementing sender and decrementing receiver
                $senderAccount->increment('balance', $transaction->amount);
                $receiverAccount->decrement('balance', $transaction->amount);
            }

            $transaction->delete();

            DB::commit();

            return response()->json(['message' => 'Transaction deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
