<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the authenticated user.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        if ($user->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $user;
    }

    /**
     * Update the authenticated user.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        if ($user->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

        return response()->json($user);
    }

    /**
     * Remove the authenticated user.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if ($user->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User account deleted successfully']);
    }
}