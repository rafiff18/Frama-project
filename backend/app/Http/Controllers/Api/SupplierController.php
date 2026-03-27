<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // List All Suppliers
    public function index()
    {
        return response()->json(\App\Models\Supplier::orderBy('created_at', 'desc')->get());
    }

    // Create Supplier
    public function store(Request $request)
    {
        $request->validate([
            'nama_suppliers' => 'required|string|max:255',
            'telepon'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string'
        ]);

        $supplier = \App\Models\Supplier::create($request->all());

        return response()->json([
            'message' => 'Supplier berhasil ditambahkan',
            'data'    => $supplier
        ], 201);
    }

    // Update Supplier
    public function update(Request $request, $id)
    {
        $supplier = \App\Models\Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_suppliers' => 'required|string|max:255',
            'telepon'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string'
        ]);

        $supplier->update($request->all());

        return response()->json([
            'message' => 'Supplier berhasil diperbarui',
            'data'    => $supplier
        ]);
    }

    // Delete Supplier
    public function destroy($id)
    {
        $supplier = \App\Models\Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier tidak ditemukan'], 404);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier berhasil dihapus']);
    }
}
