<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Total Pendapatan Keseluruhan (Hanya yang statusnya completed)
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // 2. Pendapatan Hari Ini
        $todayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', now())
            ->sum('total_price');

        // 3. Menu Paling Laku (Top 5)
        $topMenus = OrderDetail::select('menu_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->with('menu')
            ->take(5)
            ->get();

        return response()->json([
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'top_menus' => $topMenus
        ]);
    }
}
