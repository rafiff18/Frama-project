<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Penjualan;
use App\Models\Penerimaan;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Get Financial Report Data (Stats + Transactions)
     */
    public function index(Request $request)
    {
        // 1. Determine Date Range (Default: This Month)
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        if ($request->has('period')) {
            if ($request->period == 'last_month') {
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate   = Carbon::now()->subMonth()->endOfMonth();
            } elseif ($request->period == 'this_year') {
                $startDate = Carbon::now()->startOfYear();
                $endDate   = Carbon::now()->endOfYear();
            }
            // 'this_month' is default
        }

        // 2. Calculate Total Income (Penjualan)
        $totalIncome = Penjualan::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');

        // 3. Calculate Total Expense (Penerimaan / Restock)
        $totalExpense = Penerimaan::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');

        // 4. Calculate Net Profit
        $netProfit = $totalIncome - $totalExpense;

        // 5. Get Transaction History (Merged & Sorted)
        $sales = Penjualan::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'S-' . $item->id,
                    'date' => $item->created_at->format('Y-m-d H:i'),
                    'desc' => 'Penjualan Obat (' . $item->no_transaksi . ')',
                    'type' => 'income',
                    'amount' => $item->total_harga,
                    'status' => 'Selesai'
                ];
            });

        $procurements = Penerimaan::whereBetween('created_at', [$startDate, $endDate])
            ->with('supplier')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'P-' . $item->id,
                    'date' => $item->created_at->format('Y-m-d H:i'),
                    'desc' => 'Restock ' . ($item->supplier->nama_suppliers ?? 'Supplier'),
                    'type' => 'expense',
                    'amount' => $item->total_harga,
                    'status' => 'Lunas'
                ];
            });

        // Merge and Sort by Date Descending
        $transactions = $sales->concat($procurements)->sortByDesc('date')->values();

        return response()->json([
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'stats' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit,
                'is_profitable' => $netProfit >= 0
            ],
            'transactions' => $transactions
        ]);
    }

    /**
     * Generate PDF (HTML View for Print)
     * Format: Plus Minus (Debit/Credit) style
     */
    public function exportPdf(Request $request)
    {
        // Re-use logic for fetching data (DRY principle would extract this, but keeping simple for MVP)
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        // 1. Fetch Raw Data
        $sales = Penjualan::whereBetween('created_at', [$startDate, $endDate])->get();
        $procurements = Penerimaan::whereBetween('created_at', [$startDate, $endDate])->with('supplier')->get();

        // 2. Prepare View Data
        $totalDebit = $sales->sum('total_harga');
        $totalCredit = $procurements->sum('total_harga');
        $profit = $totalDebit - $totalCredit;

        // 3. Generate HTML Content for Print
        $html = '
        <html>
        <head>
            <style>
                body { font-family: sans-serif; padding: 20px; }
                h2 { text-align: center; margin-bottom: 5px; }
                p { text-align: center; color: #555; margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 13px; }
                th { background-color: #f4f4f4; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .income { color: green; }
                .expense { color: red; }
                .footer { margin-top: 30px; text-align: right; font-weight: bold; font-size: 14px; }
                .profit-box { background: #ecfdf5; padding: 10px; border: 1px solid #10b981; color: #047857; display: inline-block; }
                .loss-box { background: #fef2f2; padding: 10px; border: 1px solid #ef4444; color: #b91c1c; display: inline-block; }
            </style>
        </head>
        <body onload="window.print()">
            <h2>Laporan Keuangan Apotek</h2>
            <p>Periode: ' . $startDate->format('d M Y') . ' s/d ' . $endDate->format('d M Y') . '</p>

            <table>
                <thead>
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th width="45%">Keterangan</th>
                        <th width="20%" class="text-right">Debit (+)</th>
                        <th width="20%" class="text-right">Kredit (-)</th>
                    </tr>
                </thead>
                <tbody>';

        // Add Sales Rows
        foreach ($sales as $s) {
            $html .= '<tr>
                <td>' . $s->created_at->format('d/m/Y') . '</td>
                <td>Penjualan (' . $s->no_transaksi . ')</td>
                <td class="text-right income">Rp ' . number_format($s->total_harga, 0, ',', '.') . '</td>
                <td class="text-right">-</td>
            </tr>';
        }

        // Add Expense Rows
        foreach ($procurements as $p) {
            $supName = $p->supplier->nama_suppliers ?? 'Supplier Umum';
            $html .= '<tr>
                <td>' . $p->created_at->format('d/m/Y') . '</td>
                <td>Restock Stok (' . $supName . ')</td>
                <td class="text-right">-</td>
                <td class="text-right expense">Rp ' . number_format($p->total_harga, 0, ',', '.') . '</td>
            </tr>';
        }

        // Footer Totals
        $profitClass = $profit >= 0 ? 'profit-box' : 'loss-box';
        $profitLabel = $profit >= 0 ? 'KEUNTUNGAN BERSIH' : 'KERUGIAN';

        $html .= '
                    <tr>
                        <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL</td>
                        <td class="text-right" style="font-weight: bold;">Rp ' . number_format($totalDebit, 0, ',', '.') . '</td>
                        <td class="text-right" style="font-weight: bold;">Rp ' . number_format($totalCredit, 0, ',', '.') . '</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <div class="' . $profitClass . '">
                    ' . $profitLabel . ': Rp ' . number_format($profit, 0, ',', '.') . '
                </div>
            </div>
        </body>
        </html>';

        return response($html);
    }
}
