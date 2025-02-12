<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * 📌 ดึงรายการเมนูทั้งหมด
     */
    public function getall(Request $request)
{
    // Validate that category_id is either provided or not
    $validated = $request->validate([
        'category_id' => 'nullable|integer|exists:categories,id', // category_id is optional but must be valid if provided
    ]);

    // If category_id is provided, fetch menus for that category; otherwise, fetch all menus
    if ($validated['category_id']) {
        $menus = Menu::where('category_id', $validated['category_id'])->with('category')->get();
    } else {
        $menus = Menu::with('category')->get(); // Fetch all menus with their categories
    }

    // Check if menus exist
    if ($menus->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No menus found.'
        ], 404);
    }

    // Return the menus with success status, including category name
    return response()->json([
        'success' => true,
        'data' => $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'description' => $menu->description,
                'image' => $menu->image,
                'price' => $menu->price,
                'category_name' => $menu->category->name, // Adding category name
                'category_id' => $menu->category_id,
                'rating' => 5
            ];
        })
    ], 200);
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
            'category_id' => 'required|exists:categories,id',
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
            'image' => 'http://127.0.0.1:8000/storage/'.$imagePath,
            'category_id' => $request->category_id,
        ]);

        return response()->json([
            'message' => 'Menu created successfully!',
            'menu' => $menu,
        ], 201);
    }

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
