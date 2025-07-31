<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $banks = Bank::all();
            return response()->json($banks, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 404); // Not Found
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:banks',
                'country' => 'nullable|string|max:100',
            ]);
            $bank = Bank::create($validated);
            return response()->json($bank, 201);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return $bank;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:banks,name,' . $bank->id,
            'country' => 'nullable|string|max:100',
        ]);

        $bank->update($validated);

        return response()->json($bank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json(['message' => 'Bank not found'], 404);
        }
    
        $bank->delete();

        return response()->json(null, 204); // no Content
    }
}
