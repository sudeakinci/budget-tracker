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
            $paymentTerms = PaymentTerm::whereNull('created_by')
                ->orWhere('created_by', $user->id)
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
                    function ($attribute, $value, $fail) use ($user) {
                        $exists = PaymentTerm::where('name', $value)
                            ->where(function ($query) use ($user) {
                                $query->where('created_by', $user->id)
                                    ->orWhereNull('created_by');
                            })
                            ->exists();
                        if ($exists) {
                            $fail('This payment method already exists.');
                        }
                    },
                ],
            ]);

            $paymentTerm = PaymentTerm::create([
                'name' => $validated['name'],
                'created_by' => $user->id,
            ]);

            return response()->json($paymentTerm, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (isset($e->errors()['name'])) {
                return response()->json([
                    'message' => 'This payment method already exists.',
                    'errors' => $e->errors()
                ], 422);
            }
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
        if ($paymentTerm->created_by !== null && $paymentTerm->created_by !== $user->id) {
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
        if ($paymentTerm->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('payment_terms')->where(function ($query) use ($user) {
                    return $query->where('created_by', $user->id);
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
        if ($paymentTerm->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401); // Unauthorized
        }

        $paymentTerm->delete();

        return response()->json(null, 204); // no Content
    }
}
