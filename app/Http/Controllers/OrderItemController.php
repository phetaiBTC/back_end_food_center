<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    public function index()
    {
        return response()->json(OrderItem::all());
    }

    public function create(Request $request)
    {
        $chceckOrder = Order::where('id', $request->order_id)->first();
        if ($chceckOrder && $chceckOrder->status === 'completed') {
            return response()->json(['message' => 'Order is already completed. Cannot add item.'], 400);
        }
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
        $this->updateOrderTotal($request->order_id);
        return response()->json([
            'message' => 'Order item added successfully!',
            'orderItem' => $orderItem,
        ], 201);
    }

    private function updateOrderTotal($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }
        $totalPrice = OrderItem::where('order_id', $orderId)->sum('total_price');
        $order->update(['total_price' => $totalPrice]);
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

        // ลบ OrderItem
        $orderItem->delete();

        // คำนวณราคาใหม่ของ Order
        $order = Order::find($orderItem->order_id);
        if ($order) {
            $totalPrice = OrderItem::where('order_id', $orderItem->order_id)
                ->sum('total_price');  // คำนวณยอดรวมทั้งหมดจาก OrderItem

            // อัปเดตราคาใหม่ของ Order
            $order->total_price = $totalPrice;
            $order->save();
        }

        return response()->json(['message' => 'OrderItem deleted successfully!']);
    }
}
