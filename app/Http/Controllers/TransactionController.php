<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with(['user', 'bank'])->where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'type' => 'required|in:income,outcome',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        $amount = $validated['amount'];

        if ($validated['type'] === 'outcome') {
            if ($user->balance < $amount) {
                return response()->json(['message' => 'Insufficient balance'], 422);
            }
            $user->balance -= $amount;
        } else {
            $user->balance += $amount;
        }

        $user->save();

        $transaction = Transaction::create(array_merge($validated, ['user_id' => $user->id]));

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['user', 'bank'])->findOrFail($id);

        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $transaction;
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'bank_id' => 'sometimes|exists:banks,id',
            'type' => 'sometimes|in:income,outcome',
            'amount' => 'sometimes|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
        ]);

        $user = Auth::user();
        $oldAmount = $transaction->amount;
        $newAmount = $validated['amount'] ?? $oldAmount;

        if ($validated['type'] ?? $transaction->type === 'outcome') {
            $user->balance += $oldAmount;
            if ($user->balance < $newAmount) {
                $user->balance -= $oldAmount; // revert change
                return response()->json(['message' => 'Insufficient balance'], 422);
            }
            $user->balance -= $newAmount;
        } else {
            $user->balance -= $oldAmount;
            $user->balance += $newAmount;
        }

        $user->save();

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        if ($transaction->type === 'outcome') {
            $user->balance += $transaction->amount;
        } else {
            $user->balance -= $transaction->amount;
        }

        $user->save();

        $transaction->delete();

        return response()->json(null, 204);
    }
}