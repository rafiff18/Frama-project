<?php

namespace App\Http\Controllers\Api; // Pastikan namespace sesuai folder

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Gunakan Validator agar respon error berupa JSON yang rapi
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // Sesuaikan role dengan sistem Apotek
            'role'     => 'required|in:superadmin,apoteker,kasir' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Lebih standar di Laravel terbaru
            'role'     => $request->role
        ]);

        return response()->json([
            'message' => 'User Apotek berhasil ditambahkan',
            'data'    => $user
        ], 201);
    }
}