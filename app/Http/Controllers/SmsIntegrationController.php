<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\TransactionController;
use App\Models\SmsSettings;

class SmsIntegrationController extends Controller
{
    public function receiveSms(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Message is required'], 400);
        }
        $parsed = $this->parseSms($message, $user);

        if (!$parsed) {
            return response()->json(['error' => 'The message format is invalid'], 422);
        }

        $requestForTransaction = Request::create(
            '/dummy-url', 
            'POST',
            $parsed
        );

        $transactionController = app(TransactionController::class); // dependency injection

        return $transactionController->store($requestForTransaction);
    }

    private function parseSms($message, $user)
    {
        $settings = SmsSettings::all();

        foreach ($settings as $setting) {
            if (stripos($message, $setting->keyword) !== false) {
                // Ziraat formatına uygun şimdilik regex
                $pattern = '/(\d{2}\.\d{2}\.\d{4}) tarihinde saat (\d{2}:\d{2})\'de (\d+) nolu hesabiniza .*?FAST ile ([\d.,]+)\s*TL (gönderilmiştir|çekilmiştir)/iu';

                if (preg_match($pattern, $message, $matches)) {
                    $amount = str_replace('.', '', str_replace(',', '.', $matches[4]));
                    if ($setting->direction === 'out') {
                        $amount *= -1;
                    }

                    return [
                        'amount' => $amount,
                        'description' => $setting->bank_name . ' SMS ile işlem',
                        'payment_term_id' => $setting->payment_term_id,
                        'payment_term_name' => null, // identifies by ID
                        'user_id' => null, 
                    ];
                }
            }
        }

        return null;
    }
}
