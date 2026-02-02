<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Gunakan Transaction agar jika satu gagal, semua batal (aman)
        return DB::transaction(function () use ($request) {
            $totalPrice = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                $subtotal = $menu->price * $item['quantity'];
                $totalPrice += $subtotal;

                $orderItems[] = [
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];
            }

            $order = Order::create([
                'user_id' => auth()->id(), // Ambil ID Kasir dari Token
                'table_number' => $request->table_number,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            foreach ($orderItems as $item) {
                $order->details()->create($item);
            }

            return response()->json(['message' => 'Pesanan berhasil dibuat', 'data' => $order->load('details')], 201);
        });
    }
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,processing,ready,completed,cancelled'
    ]);

    $order = Order::findOrFail($id);
    $order->update([
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Status pesanan berhasil diperbarui',
        'data' => $order
    ]);
}
public function index()
{
    // Mengambil semua order beserta item di dalamnya dan data kasirnya
    $orders = Order::with(['details.menu', 'user'])->get();
    return response()->json($orders);
}

public function checkout(Request $request, $id)
{
    // Cari pesanan berdasarkan ID
    $order = Order::findOrFail($id);

    // Validasi agar pesanan yang sudah selesai/dibatalkan tidak bisa dicheckout lagi
    if ($order->status === 'completed' || $order->status === 'cancelled') {
        return response()->json([
            'message' => 'Pesanan ini sudah selesai atau telah dibatalkan.'
        ], 400);
    }

    // Update status menjadi completed
    $order->update([
        'status' => 'completed'
    ]);

    return response()->json([
        'message' => 'Checkout berhasil, transaksi selesai.',
        'data' => $order
    ]);
    }
}