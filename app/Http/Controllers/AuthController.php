<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Login menggunakan session (bukan token)
        Auth::login($user);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::logout(); // Logout pengguna
        return response()->json(['message' => 'Logout berhasil']);
    }
}
