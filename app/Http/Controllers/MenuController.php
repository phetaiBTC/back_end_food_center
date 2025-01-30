<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * 📌 ดึงรายการเมนูทั้งหมด
     */
    public function getAll()
    {
        return response()->json(Menu::all());
    }

    /**
     * 🛒 สร้างเมนูใหม่
     */
    public function create(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/vendors', 'public');
        }
        $menu = Menu::create([
            'vendor_id' => $request->vendor_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Menu created successfully!',
            'menu' => $menu,
        ], 201);
    }

    /**
     * 🔍 ดึงข้อมูลเมนูตาม ID
     */
    public function getOne($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        return response()->json($menu);
    }

    /**
     * ✏️ อัปเดตเมนู
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $menu->update($request->all());

        return response()->json([
            'message' => 'Menu updated successfully!',
            'menu' => $menu,
        ]);
    }

    /**
     * ❌ ลบเมนู
     */
    public function delete($id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully!']);
    }
}

