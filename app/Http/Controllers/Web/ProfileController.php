<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use \App\Models\User;

class ProfileController extends Controller
{
    public function show($id = null)
    {
        $user = Auth::user();
        $paymentTerms = PaymentTerm::where('created_by', $user->id)->get();

        return view('profile', [
            'user' => $user,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->id != $id) {
            return redirect()->route('login');
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'sometimes|string|min:8|confirmed',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return redirect()
                ->route('profile', ['id' => $user->id])
                ->with('status', 'Profile updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('profile', ['id' => $user->id])
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('profile', ['id' => $user->id])
                ->withErrors(['message' => 'An unexpected error occurred: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->id != $id) {
            return redirect()->route('login');
        }

        try {
            $user->delete();
            return redirect()->route('login')->with('status', 'Account deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('profile', ['id' => $user->id])
                ->withErrors(['message' => 'An error occurred while deleting the account.'])
                ->withInput();
        }
    }

    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $user->balance = $request->balance;
        $user->save();

        return redirect()->route('profile', ['id' => $user->id])->with('success', 'Balance updated successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $users = User::where('name', 'like', "%$query%")
            ->limit(5)
            ->get(['id', 'name']);
        return response()->json($users);
    }
}
