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
    public function index(Request $request)
    {
        // return $request->user();
        return User::all();
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, string $id)
    {
        // return $request->user();
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return $user;
    }

    /**
     * Update the authenticated user.
     */
    public function update(Request $request, string $id)
    {
        // $user = Auth::user();
        $user = User::find($id);
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
        // $user = Auth::user();
        $user = User::find($id);
        if ($user->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User account deleted successfully']);
    }
}