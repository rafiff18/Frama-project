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
            'role'     => 'required|in:superadmin,apoteker,kasir,owner' 
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

    public function index()
    {
        // Ambil semua user, urutkan dari yang terbaru
        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$id, // Ignore email sendiri
            'role'     => 'required|in:superadmin,apoteker,kasir,owner', // Tambah owner
            'password' => 'nullable|min:6' // Password opsional saat edit
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Data user berhasil diperbarui',
            'data'    => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Cegah hapus diri sendiri (opsional tapi bagus)
        if (auth()->id() == $id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus']);
    }
}