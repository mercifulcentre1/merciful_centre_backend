<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        return response()->json([
            'users' => AdminUser::orderBy('username')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:admin_users',
            'email' => 'required|email|max:100|unique:admin_users',
            'full_name' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,super_admin',
        ]);

        $user = AdminUser::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'full_name' => $validated['full_name'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = AdminUser::findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100', Rule::unique('admin_users')->ignore($user->id)],
            'full_name' => 'required|string|max:100',
            'role' => 'required|in:admin,super_admin',
            'password' => 'nullable|string|min:8',
        ]);

        $user->email = $validated['email'];
        $user->full_name = $validated['full_name'];
        $user->role = $validated['role'];
        
        if (!empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = AdminUser::findOrFail($id);

        // Prevent self-deletion
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
