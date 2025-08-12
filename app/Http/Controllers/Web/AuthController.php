<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnlockCodeMail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Str;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use \Illuminate\Http\Exceptions\ThrottleRequestsException;

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

        $token = Str::random(32);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'balance' => $validated['balance'] ?? 0,
            'email_verified_at' => null,
            'email_verification_token' => $token,
        ]);

        Mail::to($user->email)->send(new EmailVerificationMail($user));

        // Do not automatically log in the user
        // Auth::login($user); -- Removed

        return redirect('/login')->with('success', 'Account created successfully. Please check your email to verify your account before logging in.');
    } catch (ThrottleRequestsException $exception) {
        return back()->withInput()->withErrors([
            'email' => 'Too many registration attempts. Please try again later.',
        ]);
    } catch (ValidationException $e) {
        return redirect()->back()->withErrors(['message' => 'Registration failed: ' . $e->getMessage()])->withInput();
    } catch (\Exception $e) {
        return back()->withInput()->withErrors([
            'email' => 'An unexpected error occurred. Please try again.',
        ]);
    }
}

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::withTrashed()->where('email', $credentials['email'])->first();

            if ($user && !$user->email_verified_at) {
                return back()->withErrors(['email' => 'Please verify your email before logging in.']);
            }

            $remember = $request->has('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }

            if ($user && $user->trashed()) {
                return $this->sendUnlockCode($request); // send unlock code if the user is soft-deleted
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        } catch (ThrottleRequestsException $exception) {
            return back()->withInput()->withErrors([
                'email' => 'Too many login attempts. Please try again later.',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'email' => 'An unexpected error occurred. Please try again.',
            ]);
        }
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

    public function verifyEmail(Request $request)
    {
        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return redirect('/login')->with('success', 'Email verified. You can now log in.');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect('/dashboard');
        }

        // Generate a new token
        $user->email_verification_token = Str::random(32);
        $user->save();

        // Send the verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user));

        return back()->with('status', 'verification-link-sent');
    }
}