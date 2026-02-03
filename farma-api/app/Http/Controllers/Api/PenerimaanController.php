<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penerimaan;
use App\Models\PenerimaanDetail;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenerimaanController extends Controller
{
    public function index()
    {
        $data = Penerimaan::with(['details.obat', 'supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'no_faktur'   => 'required|unique:penerimaan,no_faktur',
            'items'       => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.jumlah'  => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {

                // Hitung total harga (AMAN di backend)
                $totalHarga = collect($request->items)->sum(function ($item) {
                    return $item['jumlah'] * $item['harga_satuan'];
                });

                // 2. Simpan header penerimaan
                $penerimaan = Penerimaan::create([
                    'supplier_id'    => $request->supplier_id,
                    'user_id'        => $request->user()->id,
                    'no_faktur'      => $request->no_faktur,
                    'tgl_penerimaan' => now(),
                    'total_harga'    => $totalHarga
                ]);

                // 3. Simpan detail + update stok
                foreach ($request->items as $item) {
                    PenerimaanDetail::create([
                        'penerimaan_id' => $penerimaan->id,
                        'obat_id'       => $item['obat_id'],
                        'jumlah'        => $item['jumlah'],
                        'harga_satuan'  => $item['harga_satuan']
                    ]);

                    $obat = Obat::find($item['obat_id']);
                    if ($obat) {
                        $obat->increment('stok', $item['jumlah']);
                        $obat->update([
                            'harga_beli' => $item['harga_satuan']
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'Penerimaan barang berhasil',
                    'data'    => $penerimaan->load('details')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan penerimaan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
