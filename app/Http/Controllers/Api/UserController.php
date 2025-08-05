<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

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
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
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

            return response()->json($user);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred.'], 502);
        }

    }

    /**
     * Remove the authenticated user.
     */
    public function destroy(string $id)
    {
        // $user = Auth::user();
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User account deleted successfully']);
    }
}