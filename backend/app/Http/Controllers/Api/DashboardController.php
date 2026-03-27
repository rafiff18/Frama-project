<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Obat;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Omzet Hari Ini
        $today = Carbon::today();
        $omzetHariIni = Penjualan::whereDate('created_at', $today)->sum('total_harga');

        // 2. Stok Menipis (stok <= stok_minimal)
        // Jika stok_minimal tidak ada di beberapa record, kita fallback ke <= 10
        $stokMenipis = Obat::whereColumn('stok', '<=', 'stok_minimal')
                            ->orWhere('stok', '<=', 10)
                            ->get();

        // 3. Hampir Kadaluarsa (dalam 90 hari)
        $tigaBulanKeDepan = Carbon::today()->addDays(90);
        $hampirKadaluarsa = Obat::whereNotNull('tgl_kadaluarsa')
                                ->where('tgl_kadaluarsa', '>=', $today)
                                ->where('tgl_kadaluarsa', '<=', $tigaBulanKeDepan)
                                ->orderBy('tgl_kadaluarsa', 'asc')
                                ->get();

        // 4. Total Produk
        $totalProduk = Obat::count();

        // 5. Top 5 Fast Moving Items (Bulan Ini)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $topItems = PenjualanDetail::selectRaw('obat_id, SUM(qty) as total_qty')
            ->whereHas('penjualan', function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })
            ->groupBy('obat_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('obat:id,nama_obat') // Load relasi obat untuk ambil nama
            ->get();

        // 6. Transaksi Terakhir (3 transaksi terbaru)
        $recentTransactions = Penjualan::with('details.obat')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(3)
                                        ->get()
                                        ->map(function ($trx) {
                                            // Buat format d/m/Y H:i
                                            $trx->waktu = $trx->created_at->format('H:i');
                                            
                                            // Ambil beberapa nama obat untuk deskripsi
                                            $obatNames = $trx->details->take(2)->map(function ($detail) {
                                                return $detail->obat ? $detail->obat->nama_obat : 'Obat';
                                            })->implode(', ');
                                            
                                            // Jika lebih dari 2, tambahkan "dll"
                                            if ($trx->details->count() > 2) {
                                                $obatNames .= ', dll';
                                            }
                                            
                                            $trx->deskripsi_obat = $obatNames;
                                            
                                            return $trx;
                                        });

        return response()->json([
            'success' => true,
            'data' => [
                'omzet_hari_ini' => $omzetHariIni,
                'stok_menipis_count' => $stokMenipis->count(),
                'stok_menipis_items' => $stokMenipis->take(3), // Ambil 3 untuk mini table
                'hampir_kadaluarsa_count' => $hampirKadaluarsa->count(),
                'hampir_kadaluarsa_items' => $hampirKadaluarsa->take(3), // Ambil 3 untuk mini table
                'total_produk' => $totalProduk,
                'top_items' => $topItems,
                'recent_transactions' => $recentTransactions,
            ]
        ], 200);
    }
}
