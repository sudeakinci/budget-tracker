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
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:banks',
            'code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $bank = Bank::create($validated);

        return response()->json($bank, 201);
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
            'code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $bank->update($validated);

        return response()->json($bank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();
        
        return response()->json(null, 204);
    }
}
