<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OrderItemController extends Controller
{
    /**
     * 📌 ดึงรายการ OrderItem ทั้งหมด
     */
    public function index()
    {
        return response()->json(OrderItem::all());
    }

    /**
     * 🛒 เพิ่มรายการอาหารลงในคำสั่งซื้อ
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $price = DB::table('menus')->where('id', $request->menu_id)->value('price');
        $totalItemPrice = $price * $request->quantity;

        $orderItem = OrderItem::create([
            'order_id' => $request->order_id,
            'menu_id' => $request->menu_id,
            'quantity' => $request->quantity,
            'price' => $price,
            'total_price' => $totalItemPrice,
        ]);

        return response()->json([
            'message' => 'Order item added successfully!',
            'orderItem' => $orderItem,
        ], 201);
    }

    /**
     * 🔍 ดึงรายการ OrderItem ตาม ID
     */
    public function show($id)
    {
        $orderItem = OrderItem::find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'OrderItem not found'], 404);
        }

        return response()->json($orderItem);
    }

    /**
     * ✏️ อัปเดตจำนวนสินค้าใน OrderItem
     */
    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'OrderItem not found'], 404);
        }

        $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        if ($request->has('quantity')) {
            $orderItem->quantity = $request->quantity;
            $orderItem->total_price = $orderItem->quantity * $orderItem->price;
            $orderItem->save();
        }

        return response()->json([
            'message' => 'OrderItem updated successfully!',
            'orderItem' => $orderItem,
        ]);
    }

    /**
     * ❌ ลบ OrderItem
     */
    public function destroy($id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return response()->json(['message' => 'OrderItem not found'], 404);
        }

        $orderItem->delete();

        return response()->json(['message' => 'OrderItem deleted successfully!']);
    }
}

