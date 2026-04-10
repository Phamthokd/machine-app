<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::with('roles', 'permissions')
            ->when($search, fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(20)
            ->appends(['search' => $search]);

        $permissionLabels = $this->permissionLabels();

        return view('users.index', compact('users', 'search', 'permissionLabels'));
    }

    public function create()
    {
        $roles = Role::all();
        $departments = $this->departments();
        $permissionGroups = config('feature_permissions', []);

        return view('users.create', compact('roles', 'departments', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'managed_department' => ['nullable', 'string', Rule::in($this->departments())],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'managed_department' => $validated['managed_department'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->assignRole($validated['role']);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect('/users')->with('success', __('messages.user_created_success'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = $this->departments();
        $permissionGroups = config('feature_permissions', []);
        $user->load('permissions');

        return view('users.edit', compact('user', 'roles', 'departments', 'permissionGroups'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:6'],
            'managed_department' => ['nullable', 'string', Rule::in($this->departments())],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'managed_department' => $validated['managed_department'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect('/users')->with('success', __('messages.user_updated_success'));
    }

    public function toggleActive(User $user)
    {
        abort_if(auth()->id() === $user->id, 422, __('messages.error_cannot_self_deactivate'));

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return redirect('/users')->with(
            'success',
            $user->is_active ? __('messages.user_activated_success') : __('messages.user_deactivated_success')
        );
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect('/users')->with('success', __('messages.user_deleted_success'));
    }

    private function departments(): array
    {
        return [
            'QA',
            'Thu mua',
            'Kho cơ khí',
            'Công trình + cơ điện',
            'Phòng thí nghiệm',
            'Nhân quyền',
            'Nhân sự',
            'Hành chính',
            'XNK',
            'Xưởng 6 Tầng 1',
            'Xưởng 6 Tầng 2',
            'Xưởng 5',
            'Bán thành phẩm',
            'Phòng mẫu',
            'Kiểm vải',
            'Thêu',
            'May lập trình',
            'Kế toán',
            'Sale',
            'Đơn hàng',
            'Kho vải + PL',
            'Nhà cắt',
            'Nhà giặt',
            'Thống kê tổng',
            'IE',
            'KHSX',
            'IT',
            'Sửa máy',
            'Khác',
        ];
    }

    private function permissionLabels(): array
    {
        return collect(config('feature_permissions', []))
            ->flatMap(fn ($group) => $group['items'] ?? [])
            ->all();
    }
}
