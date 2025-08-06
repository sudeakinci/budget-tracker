<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['auth'];
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $transactions = Transaction::with(['owner', 'user'])
            ->where(function ($query) use ($user) {
                $query->where('owner', $user->id)
                    ->orWhere('user_id', $user->id);
            })
            ->select(
                'transactions.*',
                DB::raw(
                    'CASE 
                            WHEN transactions.owner = ' . $user->id . ' THEN transactions.amount * -1
                            ELSE transactions.amount
                            END as amount'
                )
            )
            ->orderByDesc('created_at')->paginate(20);



        $users = User::where('id', '!=', $user->id)->get();
        $paymentTerms = PaymentTerm::whereNull('created_by')
            ->orWhere('created_by', $user->id)
            ->get();

        return view('transactions', [
            'transactions' => $transactions,
            'users' => $users,
            'paymentTerms' => $paymentTerms,
            'balance' => $user->balance,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'receiver_type' => 'required|in:select,custom',
            'payment_type' => 'required|in:select,custom',
            'user_id' => 'required_if:receiver_type,select|nullable|exists:users,id',
            'custom_user' => 'required_if:receiver_type,custom|nullable|string|max:255',
            'payment_term_id' => 'required_if:payment_type,select|nullable|exists:payment_terms,id',
            'payment_term_name' => 'required_if:payment_type,custom|nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $owner = $user;

            if ($owner->balance < $validatedData['amount']) {
                return redirect()->back()->withErrors(['message' => 'Yetersiz bakiye']);
            }

            $paymentTermId = $validatedData['payment_term_id'] ?? null;
            $paymentTermName = $validatedData['payment_term_name'] ?? null;

            // Eğer payment_term_name boşsa, mevcut payment_term'in adını al
            if (!$paymentTermName) {
                $paymentTerm = PaymentTerm::findOrFail($paymentTermId);
                $paymentTermName = $paymentTerm->name;
            }

            // Transaction oluştur
            $transaction = Transaction::create([
                'owner' => $owner->id,
                'user_id' => $validatedData['user_id'] ?? null,
                'payment_term_id' => $paymentTermId,
                'payment_term_name' => $paymentTermName,
                'description' => $validatedData['description'] ?? null,
                'amount' => $validatedData['amount'],
            ]);

            if (!$transaction) {
                DB::rollBack();
                return redirect()->back()->withErrors(['message' => 'İşlem oluşturulamadı']);
            }

            // Gönderen kullanıcının bakiyesini güncelle
            $owner->balance -= $validatedData['amount'];
            $owner->save();

            // Eğer alıcı kullanıcı belirtilmişse, onun bakiyesini güncelle
            if (isset($validatedData['user_id'])) {
                $receiver = User::find($validatedData['user_id']);
                $receiver->balance += $validatedData['amount'];
                $receiver->save();
            }

            DB::commit();
            return redirect()->route('transactions')->with('status', 'İşlem başarıyla gerçekleşti.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()]);
        }
    }
}
