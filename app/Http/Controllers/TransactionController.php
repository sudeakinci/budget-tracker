<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with(['user', 'bank'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bank_id' => 'required|exists:banks,id',
            'type' => 'required|in:income,outcome',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $transaction = Transaction::create($validated);
        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        return Transaction::with(['user', 'bank'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'bank_id' => 'sometimes|exists:banks,id',
            'type' => 'sometimes|in:income,outcome',
            'amount' => 'sometimes|numeric',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        Transaction::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
