<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentTerm;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all payment terms created by the authenticated user
        $paymentTerms = PaymentTerm::where('created_by', $user->id)
            ->withCount('transactions')
            ->orderBy('name')
            ->get();
            
        // Get all transactions that use any payment term
        $transactions = Transaction::with(['paymentTerm', 'user', 'owner'])
            ->where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->whereNotNull('payment_term_id')
            ->select(
                'transactions.*',
                \DB::raw(
                    'CASE 
                           WHEN transactions.owner = ' . $user->id . ' AND transactions.is_sms = false 
                           THEN transactions.amount * -1
                           ELSE transactions.amount
                           END as display_amount'
                )
            )
            ->orderByDesc('created_at')
            ->paginate(20);
            
        return view('payment-terms', compact('paymentTerms', 'transactions'));
    }

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
