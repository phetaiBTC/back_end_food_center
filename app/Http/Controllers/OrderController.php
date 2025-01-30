<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 🛒 สร้างคำสั่งซื้อใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,id',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;

        // สร้างคำสั่งซื้อใหม่
        $order = Order::create([
            'user_id' => $request->user_id,
            'vendor_id' => $request->vendor_id,
            'total_price' => 0, // คำนวณภายหลัง
            'status' => 'pending',
        ]);

        // เพิ่มรายการอาหารที่สั่ง
        foreach ($request->items as $item) {
            $price = DB::table('menus')->where('id', $item['menu_id'])->value('price');
            $totalItemPrice = $price * $item['quantity'];
            $totalPrice += $totalItemPrice;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $price,
                'total_price' => $totalItemPrice,
            ]);
        }

        // อัปเดตยอดรวม
        $order->update(['total_price' => $totalPrice]);

        return response()->json([
            'message' => 'Order created successfully!',
            'order' => $order->load('items'),
        ], 201);
    }

    /**
     * 📌 ดึงรายการคำสั่งซื้อทั้งหมด
     */
    public function index()
    {
        $orders = Order::with('items')->get();
        return response()->json($orders);
    }

    /**
     * 🔍 ดึงรายละเอียดคำสั่งซื้อเฉพาะ ID
     */
    public function show($id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    /**
     * ✅ อัปเดตสถานะคำสั่งซื้อ
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,completed,cancelled',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully!',
            'order' => $order,
        ]);
    }

    /**
     * ❌ ลบคำสั่งซื้อ
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully!']);
    }
}
