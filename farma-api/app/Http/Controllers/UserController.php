<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:owner,admin,kasir,chef,superadmin' // Sesuai role sistem
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => trim($validated['role']) // Antisipasi karakter tersembunyi
        ]);

        return response()->json(['message' => 'User berhasil ditambahkan', 'data' => $user], 201);
    }
}
