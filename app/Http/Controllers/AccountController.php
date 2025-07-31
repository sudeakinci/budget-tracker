<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    /**
     * Display a listing of the accounts.
     *
     */
    public function index()
    {
        try {
            $accounts = Account::with('bank')->get();
            return response()->json($accounts, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 404); // Not Found
        }
    }

    /**
     * Store a newly created account in storage.
     *
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'bank_id' => 'required|exists:banks,id',
                'balance' => 'required|numeric|min:0',
            ]);

            $account = Account::create($validatedData);
            return response()->json($account, 201); // Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422); // Unprocessable Content
        }
    }

    /**
     * Display the specified account.
     *
     */
    public function show(Account $account)
    {
        return response()->json($account);
    }

    /**
     * Update the specified account in storage.
     *
     */
    public function update(Request $request, Account $account)
    {
        try {
            $validatedData = $request->validate([
                'bank_id' => 'sometimes|required|exists:banks,id',
                'balance' => 'sometimes|required|numeric|min:0',
            ]);

            $account->update($validatedData);
            return response()->json($account);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422); // Unprocessable Content
        }
    }

    /**
     * Remove the specified account from storage.
     *
     */
    public function destroy($id)
    {
        $account = Account::find($id);

        if(!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $account->delete();
        return response()->json(null, 204);
    }
}
