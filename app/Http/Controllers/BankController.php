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
        return Bank::all();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = request()->validate([
            'name' => 'required|string',
            'branch' => 'required|string',
            'account_number' => 'nullable|string',
            'iban' => 'nullable|string',
        ]);
        $bank = Bank::create($validated);
        return response()->json($bank, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Bank::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bank = Bank::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'branch' => 'sometimes|string',
            'account_number' => 'nullable|string',
            'iban' => 'nullable|string',
        ]);

        $bank->update($validated);

        return response()->json($bank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Bank::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
