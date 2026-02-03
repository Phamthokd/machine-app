@extends('layouts.app-simple')
@section('title', 'Quản lý người dùng')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold">Danh sách người dùng</h4>
    <a href="/users/create" class="btn btn-primary shadow-sm tap d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Thêm mới</span>
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-4 text-secondary text-xs uppercase font-weight-bolder opacity-7">Họ tên</th>
                    <th class="py-3 text-secondary text-xs uppercase font-weight-bolder opacity-7">Tên đăng nhập</th>
                    <th class="py-3 text-secondary text-xs uppercase font-weight-bolder opacity-7">Chức vụ</th>
                    <th class="py-3 text-end pe-4 text-secondary text-xs uppercase font-weight-bolder opacity-7">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td>{{ $user->username }}</td>
                    <td>
                        @php
                            $roleMap = [
                                'admin' => 'Admin',
                                'warehouse' => 'Kho',
                                'team_leader' => 'Tổ trưởng',
                                'repair_tech' => 'Sửa máy',
                            ];
                        @endphp
                        @foreach($user->roles as $role)
                            <span class="badge bg-info bg-opacity-10 text-info">{{ $roleMap[$role->name] ?? $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="text-end pe-4">
                        <a href="/users/{{ $user->id }}/edit" class="btn btn-sm btn-light text-primary fw-semibold">
                            Sửa
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="p-3">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
