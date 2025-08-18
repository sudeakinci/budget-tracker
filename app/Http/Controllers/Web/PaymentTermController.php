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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all payment terms created by the authenticated user
        $paymentTerms = PaymentTerm::where('created_by', $user->id)
            ->withCount('transactions')
            ->orderBy('name')
            ->get();
            
        $amountType = $request->input('amount_type', 'all');
        $receiverFilter = $request->input('receiver');
        $paymentTermId = $request->input('payment_term_id'); // For backward compatibility
        $paymentTermIds = $request->input('payment_term_ids');

        $dateRange = $request->input('date_range');
        $startDate = now()->startOfYear()->format(format: 'Y-m-d');
        $endDate = now()->format('Y-m-d');

        if ($dateRange) {
            $dates = explode(' to ', $dateRange);
            $startDate = $dates[0];
            $endDate = $dates[1] ?? $dates[0];
        }
            
        // Get all transactions that use any payment term
        $query = Transaction::with(['paymentTerm', 'user', 'owner'])
            ->forUser($user->id)
            ->dateRange($startDate, $endDate)
            ->withDisplayAmount($user->id)
            ->filterAmountType($user->id, $amountType)
            ->when($receiverFilter, fn($q) => $q->filterReceiver(explode(',', $receiverFilter)))
            ->orderByDesc('created_at')
            ->whereNotNull('payment_term_id');
            
        // Apply payment term filter if provided
        if ($paymentTermIds) {
            $ids = explode(',', $paymentTermIds);
            $query->whereIn('payment_term_id', $ids);
        } elseif ($paymentTermId) {
            // For backward compatibility
            $query->where('payment_term_id', $paymentTermId);
        }
            
        $transactions = $query->orderByDesc('created_at')
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
