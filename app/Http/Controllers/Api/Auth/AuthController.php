<?php
namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }

        $token = auth()->user()->createToken('Token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        // Menghapus semua token yang terkait dengan pengguna yang sedang masuk
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully',
            'data' => null,
        ], 200);
    }
}
