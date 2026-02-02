<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    // 1. Tampilkan Semua Menu
    public function index()
    {
        $menus = Menu::all();
        return response()->json($menus);
    }

    // 2. Simpan Menu Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $validated['image'] = $path;
        }

        $menu = Menu::create($validated);
        return response()->json(['message' => 'Menu berhasil dibuat', 'data' => $menu], 201);
    }

    // 3. Detail Menu
    public function show($id)
    {
        $menu = Menu::find($id);
        if (!$menu) return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        return response()->json($menu);
    }

    // 4. Update Menu
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric',
            'category' => 'string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            
            $path = $request->file('image')->store('menus', 'public');
            $validated['image'] = $path;
        }

        $menu->update($validated);
        return response()->json(['message' => 'Menu berhasil diupdate', 'data' => $menu]);
    }

    // 5. Hapus Menu
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        if ($menu->image) Storage::disk('public')->delete($menu->image);
        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus']);
    }
}