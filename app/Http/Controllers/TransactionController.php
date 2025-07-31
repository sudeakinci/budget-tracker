<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // $user = Auth::user();
        // if (!$user) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        $userId = $request->input('user_id');

        try {
            $query = Transaction::with(['owner', 'user']);
            // ->where(function ($q) use ($user) {
            //     $q->where('owner', $user->id)
            //         ->orWhere('user_id', $user->id);
            // })
            // ->select(
            //     'transactions.*',
            //     DB::raw(
            //         'CASE 
            //                 WHEN transactions.owner = ' . $user->id . ' THEN transactions.amount * -1
            //                 ELSE transactions.amount
            //                 END as amount'
            //     )
            // );
            if ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('owner', $userId)
                        ->orWhere('user_id', $userId);
                })
                    ->select(
                        'transactions.*',
                        DB::raw(
                            'CASE 
                            WHEN transactions.owner = ' . $userId . ' THEN transactions.amount * -1
                            ELSE transactions.amount
                        END as amount'
                        )
                    );
            }

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
            'payment_term' => 'required|string',
        ]);
        try {
            // $owner = Auth::user();
            // if (!$owner) {
            //     return response()->json(['message' => 'Unauthorized'], 401);
            // }
            // $validatedData = $request->validate([
            //     'owner' => 'required|exists:users,id',
            //     'user_id' => 'nullable|exists:users,id',
            //     'amount' => 'required|numeric|min:0.01',
            //     'description' => 'nullable|string',
            //     'payment_term' => 'required|string',
            // ]);

            DB::beginTransaction();

            $transaction = Transaction::create([
                // 'owner' => $owner->id,
                'owner' => $validatedData['owner'],
                'user_id' => $validatedData['user_id'] ?? null,
                'amount' => $validatedData['amount'],
                'description' => $validatedData['description'],
                'payment_term' => $validatedData['payment_term'],
            ]);

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
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
        // $user = Auth::user();
        // if (!$user) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        //only owner can delete the transaction
        // if ($transaction->owner != $user->id) {
        //     return response()->json(['message' => 'You are not authorized to delete this transaction.'], 403);
        // }

        try {
            $transaction->delete();
            return response()->json(['message' => 'Transaction deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 502);
        }
    }
}
