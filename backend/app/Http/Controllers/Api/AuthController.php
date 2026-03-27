<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Tambahkan ini!

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cek Login
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau Password salah'
            ], 401);
        }

        /** @var \App\Models\User $user */ // 💡 Rahasia agar Intelephense tidak error
        $user = Auth::user(); 
        
        // 3. Buat Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Kirim Respon (Format standard Sanctum)
        return response()->json([
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user // Mengirimkan info nama & role (apoteker/kasir/admin)
        ]);
    }
    
    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        
        // Hapus token yang sedang digunakan
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}