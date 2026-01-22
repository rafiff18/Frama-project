<?php

namespace App\Http\Controllers; // ✅ Hapus \Auth agar sesuai standar

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cek Login (Otomatis cek hash password)
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau Password salah'
            ], 401);
        }

        // 3. Ambil User & Buat Token
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Kirim Respon (Sesuai format yang diminta Vue)
        return response()->json([
            'message' => 'Login Berhasil',
            'access_token' => $token, // ✅ Kunci harus 'access_token'
            'token_type' => 'Bearer',
            'user' => $user // ✅ Kirim full data user (biar role, nama, email kebawa)
        ]);
    }
    
    // Tambahan Logout (Penting nanti)
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}