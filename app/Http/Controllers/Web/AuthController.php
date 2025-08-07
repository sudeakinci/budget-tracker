<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnlockCodeMail;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function showRegistrationForm()
    {
        return view("auth.register");
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'balance' => 'nullable|numeric|min:0',

            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'balance' => $validated['balance'] ?? 0,
            ]);

            Auth::login($user); // automatically log in the user after registration, starting their session

            return redirect('/dashboard')->with('success', 'Account created successfully');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', 'Registration failed');
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::withTrashed()->where('email', $credentials['email'])->first();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        if ($user && $user->trashed()) {
            return $this->sendUnlockCode($request); // send unlock code if the user is soft-deleted
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }


    // unlock account functions
    public function showUnlockForm(Request $request)
    {
        return view('auth.verify-unlock-code', ['email' => $request->email]);
    }

    public function sendUnlockCode(Request $request)
    {
        $user = User::withTrashed()->where('email', $request->email)->firstOrFail();

        $code = rand(100000, 999999); // 6-digit code

        $expiresAt = now()->addMinutes(3);

        $user->code = $code;
        $user->expires_at = $expiresAt;
        $user->save();

        Mail::to($user->email)->send(new UnlockCodeMail($code));

        session(['unlock_code_expires_at' => $expiresAt->timestamp]);


        return view('auth.verify-unlock-code', ['email' => $user->email])->with('success', 'New code sent to your email');
    }

    public function verifyUnlockCode(Request $request)
    {
        try {
            $user = User::withTrashed()->where('email', $request->email)->first();

            if (!$user) {
                return redirect()->route('unlock.account.request', ['email' => $request->email])
                    ->with('error', 'Kullanıcı bulunamadı.');
            }

            $valid = $user->code == $request->code && $user->expires_at > now();

            if (!$valid) {
                return redirect()->route('unlock.account.request', ['email' => $request->email])
                    ->with('error', 'Invalid or expired code.');
            }

            $user->restore();
            Auth::login($user);

            // Kod ve süresini sıfırla
            $user->code = null;
            $user->expires_at = null;
            $user->save();


            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('unlock.account.request', ['email' => $request->email])
                ->with('error', 'An error occurred, please try again.');
        }
    }
}