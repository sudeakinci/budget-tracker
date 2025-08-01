<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $paymentTerms = PaymentTerm::whereNull('user_id')
                ->orWhere('user_id', $user->id)
                ->get();
            return response()->json($paymentTerms, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('payment_terms')->where(function ($query) use ($user) {
                        return $query->where('user_id', $user->id);
                    }),
                ],
            ]);

            $paymentTerm = PaymentTerm::create([
                'name' => $validated['name'],
                'user_id' => $user->id,
            ]);

            return response()->json($paymentTerm, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
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
        $user = Auth::user();
        if ($paymentTerm->user_id !== null && $paymentTerm->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $paymentTerm;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $user = Auth::user();
        if ($paymentTerm->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('payment_terms')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })->ignore($paymentTerm->id),
            ],
        ]);

        $paymentTerm->update($validated);

        return response()->json($paymentTerm);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentTerm $paymentTerm)
    {
        $user = Auth::user();
        if ($paymentTerm->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $paymentTerm->delete();

        return response()->json(null, 204); // no Content
    }
}
