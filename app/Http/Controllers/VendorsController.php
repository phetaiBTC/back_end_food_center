<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Vendors;
use Illuminate\Support\Facades\Storage;

class VendorsController extends Controller
{
    public function getAll(Request $request)
    {
        // $vendors = Vendors::where('name', 'LIKE', "%{$request->serach}%")
        //     ->get();
        $vendors = Vendors::where('name', 'LIKE', "%{$request->search}%")->get();

        $vendorsData = $vendors->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'description' => $vendor->description,
                'phone' => $vendor->phone,
                'number' => $vendor->number,
                'image' => $vendor->image,
                'created_at' => $vendor->created_at,
                'updated_at' => $vendor->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $vendorsData,
        ]);
    }
    public function getOne($id)
    {
        $vendor = Vendors::find($id);
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $vendor]);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'phone' => 'required|string|max:11|min:11',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $number = "";
        $checknumber = Vendors::count();

        if ($checknumber == 0) {
            $number = "P-0001";
        } else {
            $number = "P-" . sprintf("%04d", $checknumber + 1);
        }
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/vendors', 'public');
        }

        $vendor = new Vendors();
        $vendor->name = $request->name;
        $vendor->description = $request->description;
        $vendor->phone = $request->phone;
        $vendor->number = $number;
        $vendor->image = asset('storage/' . $imagePath);

        $vendor->save();

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }


    public function update(Request $request, $id)
    {
        $vendor = Vendors::find($id);
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:11|min:11',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        if ($request->hasFile('image')) {
            if ($vendor->image) {
                $oldImagePath = str_replace(asset('storage/'), '', $vendor->image);
                Storage::disk('public')->delete($oldImagePath);
            }
            $imagePath = $request->file('image')->store('uploads/vendors', 'public');
            $vendor->image = asset('storage/' . $imagePath);
        } else {
            $vendor->image = $vendor->image;
        }

        $vendor->name = $request->name;
        $vendor->description = $request->description;
        $vendor->phone = $request->phone;
        $vendor->save();

        return response()->json(['success' => true, 'data' => $vendor]);
    }
    public function delete($id)
    {
        $vendor = Vendors::find($id);
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
        }
        $vendor->delete();
        return response()->json(['success' => true, 'message' => 'Vendor deleted successfully'], 200);
    }
}
