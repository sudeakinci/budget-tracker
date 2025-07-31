<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $paymentTerms = PaymentTerm::all();
            return response()->json($paymentTerms, 200);
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
                'name' => 'required|string|max:255|unique:payment_terms',
            ]);
            $paymentTerm = PaymentTerm::create($validated);
            return response()->json($paymentTerm, 201);
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
    public function show(PaymentTerm $paymentTerm)
    {
        return $paymentTerm;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:payment_terms,name,' . $paymentTerm->id,
        ]);

        $paymentTerm->update($validated);

        return response()->json($paymentTerm);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $paymentTerm = PaymentTerm::find($id);

        if (!$paymentTerm) {
            return response()->json(['message' => 'Payment Term not found'], 404);
        }

        $paymentTerm->delete();

        return response()->json(null, 204); // no Content
    }
}
