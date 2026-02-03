<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ObatController extends Controller
{
    // 1. Lihat Semua Obat (Bisa Search)
    public function index(Request $request)
    {
        $query = Obat::query();

        // Fitur pencarian berdasarkan nama atau kode obat
        if ($request->has('search')) {
            $query->where('nama_obat', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_obat', 'like', '%' . $request->search . '%');
        }

        return response()->json([
            'success' => true,
            'data'    => $query->latest()->get()
        ]);
    }

    // 2. Tambah Obat Baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_obat'      => 'required|unique:obat,kode_obat',
            'nama_obat'      => 'required|string',
            'kategori'       => 'required',
            'satuan'         => 'required',
            'stok_minimal'   => 'required|integer',
            'harga_beli'     => 'required|numeric',
            'harga_jual'     => 'required|numeric',
            'tgl_kadaluarsa' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        /** @var \App\Models\Obat $obat */
        $obat = Obat::create($request->all());

        return response()->json([
            'message' => 'Obat berhasil ditambahkan',
            'data'    => $obat
        ], 201);
    }

    // 3. Update Data Obat
    public function update(Request $request, $id)
    {
        $obat = Obat::find($id);

        if (!$obat) {
            return response()->json(['message' => 'Obat tidak ditemukan'], 404);
        }

        $obat->update($request->all());

        return response()->json([
            'message' => 'Data obat berhasil diupdate',
            'data'    => $obat
        ]);
    }

    // 4. Hapus Obat
    public function destroy($id)
    {
        $obat = Obat::find($id);

        if (!$obat) {
            return response()->json(['message' => 'Obat tidak ditemukan'], 404);
        }

        $obat->delete();

        return response()->json(['message' => 'Obat berhasil dihapus']);
    }
}