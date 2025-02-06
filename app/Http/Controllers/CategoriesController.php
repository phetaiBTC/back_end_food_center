<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function getall(){
        $categorise = Category::all();
        return response()->json($categorise);
    }

    public function create(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = Category::create([
            'name' => $request->name,
        ]);
        return response()->json($category);
    }

    public function getOne($id){
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    public function update(Request $request, $id){
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    public function delete($id){
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully!']);
    }
    
}
