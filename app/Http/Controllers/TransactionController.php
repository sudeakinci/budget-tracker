<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // $query = Auth::user()->transactions()->with(['user', 'bank']);
        $query = Transaction::with(['user', 'bank']);

        // Optional: Filter by is_income (true/false)
        if ($request->has('is_income')) {
            $query->where('is_income', filter_var($request->input('is_income'), FILTER_VALIDATE_BOOLEAN));
        }

        // Optional: Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
        }

        return $query->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', //remove this line for auth-less testing
            'bank_id' => 'required|exists:banks,id',
            'is_income' => 'required|boolean',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // $user = Auth::user();
        $user = \App\Models\User::find($validated['user_id']);
        $bank = Bank::find($validated['bank_id']);

        // bank must belong to the authenticated user
        if (!$bank || $bank->user_id !== $user->id) {
            // return response()->json(['message' => 'Invalid bank selected'], 422);
            return response()->json(['message' => 'Invalid bank selected for this user'], 422);
        }

        try {
            DB::beginTransaction();

            $amount = $validated['amount'];

            if (!$validated['is_income']) {
                // if ($user->balance < $amount) {
                //     DB::rollBack();
                //     return response()->json(['message' => 'Insufficient balance'], 422);
                // }
                $user->decrement('balance', $amount);
            } else {
                $user->increment('balance', $amount);
            }

            $transaction = $user->transactions()->create($validated);

            DB::commit();

            return response()->json($transaction, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with(['user', 'bank'])->find($id);

        // if (!$transaction || $transaction->user_id !== Auth::id()) {
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return $transaction;
    }
}