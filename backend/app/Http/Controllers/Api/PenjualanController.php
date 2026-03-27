<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    public function index()
    {
        return response()->json(
            Penjualan::with(['details.obat', 'user'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bayar' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            return DB::transaction(function () use ($request) {

                $totalHarga = 0;

                // 1. cek stok
                foreach ($request->items as $item) {
                    $obat = Obat::find($item['obat_id']);

                    if ($obat->stok < $item['jumlah']) {
                        return response()->json([
                            'message' => 'Stok tidak cukup untuk obat: ' . $obat->nama_obat
                        ], 400);
                    }
                }

                // 2. hitung total
                foreach ($request->items as $item) {
                    $obat = Obat::find($item['obat_id']);
                    $totalHarga += $obat->harga_jual * $item['jumlah'];
                }

                // 3. validasi bayar
                if ($request->bayar < $totalHarga) {
                    return response()->json([
                        'message' => 'Uang bayar kurang'
                    ], 400);
                }

                $kembali = $request->bayar - $totalHarga;

                // 4. simpan penjualan
                $penjualan = Penjualan::create([
                    'user_id'      => $request->user()->id,
                    'no_transaksi' => 'TRX-' . now()->format('YmdHis'),
                    'total_harga'  => $totalHarga,
                    'bayar'        => $request->bayar,
                    'kembali'      => $kembali
                ]);

                // 5. simpan detail + kurangi stok
                foreach ($request->items as $item) {
                    $obat = Obat::find($item['obat_id']);

                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'obat_id'      => $obat->id,
                        'qty'          => $item['jumlah'],
                        'harga'        => $obat->harga_jual
                    ]);

                    $obat->decrement('stok', $item['jumlah']);
                }

                return response()->json([
                    'message' => 'Penjualan berhasil',
                    'data' => $penjualan->load('details.obat')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal melakukan penjualan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
