<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::with('roles')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends(['search' => $search]);
        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        $departments = ['Xưởng 6 Tầng 1', 'Xưởng 6 Tầng 2', 'Xưởng 5', 'Bán thành phẩm', 'Phòng mẫu', 'Kiểm vải', 'Khác'];
        return view('users.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|exists:roles,name',
            'managed_department' => 'nullable|string|in:Xưởng 6 Tầng 1,Xưởng 6 Tầng 2,Xưởng 5,Bán thành phẩm,Phòng mẫu,Kiểm vải,Khác',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'managed_department' => $validated['managed_department'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return redirect('/users')->with('success', 'Tạo người dùng thành công');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = ['Xưởng 6 Tầng 1', 'Xưởng 6 Tầng 2', 'Xưởng 5', 'Bán thành phẩm', 'Phòng mẫu', 'Kiểm vải', 'Khác'];
        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role' => 'required|string|exists:roles,name',
            'password' => 'nullable|string|min:6',
            'managed_department' => 'nullable|string|in:Xưởng 6 Tầng 1,Xưởng 6 Tầng 2,Xưởng 5,Bán thành phẩm,Phòng mẫu,Kiểm vải,Khác',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'managed_department' => $validated['managed_department'] ?? null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        return redirect('/users')->with('success', 'Cập nhật người dùng thành công');
    }
}
