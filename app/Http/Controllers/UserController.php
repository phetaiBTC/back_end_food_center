<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        // Retrieve the search query from the request
        $search = $request->query('search', '');

        // Search users by name or email
        $users = User::with('dristric')  // Eager load the 'dristric' relationship
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->get();

        // Modify the response to include the district name
        $usersData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'dristric_name' => $user->dristric ? $user->dristric->dr_name : null,
                'province_name' => $user->dristric && $user->dristric->province ? $user->dristric->province->pr_name : null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $usersData,
        ]);
    }
    public function getOne($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }
    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $user->update($request->all());

        return response()->json(['success' => true, 'data' => $user]);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $user->update([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
            'password' => $request->filled('password') ? bcrypt($request->input('password')) : $user->password,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ], 200);
    }
    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);
    }
    public function tokenDetails(Request $request)
    {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token);

        return response()->json([
            'expires_at' => $payload->get('exp'), // Timestamp ของวันหมดอายุ
            'issued_at' => $payload->get('iat'), // Timestamp ของวันที่ออก token
        ]);
    }
}
