<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $user = Auth::user();
        if ($paymentTerm->created_by !== $user->id) {
            return redirect()->back()->withErrors(['message' => 'Yetkisiz işlem']);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $paymentTerm->update($validated);
        return redirect()->route('profile', ['id' => $user->id])->with('status', 'Payment term updated.');
    }

    public function destroy(PaymentTerm $paymentTerm)
    {
        $user = Auth::user();
        if ($paymentTerm->created_by !== $user->id) {
            return redirect()->back()->withErrors(['message' => 'Yetkisiz işlem']);
        }
        if ($paymentTerm->transactions()->count() > 0) {
            return redirect()->back()->withErrors(['message' => 'This payment term is used in transactions and cannot be deleted.']);
        }
        $paymentTerm->delete();
        return redirect()->route('profile', ['id' => $user->id])->with('status', 'Payment term deleted.');
    }
}
