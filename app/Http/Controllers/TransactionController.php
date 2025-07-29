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
        $query = Auth::user()->transactions()->with(['user', 'bank']);

        // Optional: Filter by type (income/outcome)
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
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
            'bank_id' => 'required|exists:banks,id',
            'type' => 'required|in:income,outcome',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        $bank = Bank::find($validated['bank_id']);

        // bank must belong to the authenticated user
        if (!$bank || $bank->user_id !== $user->id) {
            return response()->json(['message' => 'Invalid bank selected'], 422);
        }

        try {
            DB::beginTransaction();

            $amount = $validated['amount'];

            if ($validated['type'] === 'outcome') {
                if ($user->balance < $amount) {
                    DB::rollBack();
                    return response()->json(['message' => 'Insufficient balance'], 422);
                }
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

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return $transaction;
    }

    public function createFromSms(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,outcome',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        try {
            DB::beginTransaction();

            $bank = Bank::firstOrCreate(
                ['user_id' => $user->id, 'name' => $validated['bank_name']]
            );

            if ($validated['type'] === 'outcome') {
                if ($user->balance < $validated['amount']) {
                    DB::rollBack();
                    return response()->json(['message' => 'Insufficient balance'], 422);
                }
                $user->decrement('balance', $validated['amount']);
            } else {
                $user->increment('balance', $validated['amount']);
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'bank_id' => $bank->id,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? 'Transaction from SMS',
                'date' => $validated['date'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transaction created successfully from SMS',
                'transaction' => $transaction
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}