<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use \App\Models\PaymentTerm;
use Auth;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SmsSettings;
use App\Http\Controllers\Controller;

class SmsIntegrationController extends Controller
{
    public function receiveSms(Request $request, SmsSettings $smsSetting)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Message is required'], 400);
        }
        $parsed = $this->parseSms($message, $smsSetting);

        if (!$parsed || isset($parsed['error'])) {
            return response()->json(['error' => 'The message format is invalid'], 422);
        }

        try {
            DB::beginTransaction();

            $paymentTerm = PaymentTerm::findOrFail($parsed['payment_term_id']);

            $transaction = Transaction::create([
                'owner' => $user->id,
                'amount' => $parsed['amount'],
                'description' => $parsed['description'],
                'payment_term_id' => $parsed['payment_term_id'],
                'payment_term_name' => $paymentTerm->name,
            ]);

            $user->balance += $parsed['amount'];
            $user->save();

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An unexpected error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    private function parseSms($message, SmsSettings $settings)
    {

        $bank_patterns = [
            'ZIRAAT' => [
                'pattern' => '/(\d{2}\.\d{2}\.\d{4})(?: tarihinde)? saat (\d{2}:\d{2})\'de (\d+) nolu hesab(?:iniza|inizdan).*?FAST ile.*?([\d.,]+)\s*TL (gonderilmistir|aktarilmistir)/iu',
                'handler' => function ($matches) use ($settings) {
                    $amount = (float) str_replace(',', '.', str_replace('.', '', $matches[4]));
                    if (mb_strtolower($matches[5]) === 'aktarilmistir') {
                        $amount *= -1;
                    }
                    return [
                        'amount' => $amount,
                        'description' => $settings->bank_name . ' SMS',
                        'payment_term_id' => $settings->payment_term_id,
                        'user_id' => null,
                    ];
                }
            ],
        ];

        foreach ($bank_patterns as $bank_pattern) {
            if (preg_match($bank_pattern['pattern'], $message, $matches)) {
                return $bank_pattern['handler']($matches);
            }
        }
        return null;
    }
}
