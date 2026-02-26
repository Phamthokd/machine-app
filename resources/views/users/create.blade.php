@extends('layouts.app-simple')
@section('title', 'Thêm người dùng')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/users" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">Thêm người dùng mới</h4>
</div>

<div class="card border-0 shadow-sm rounded-4" style="max-width: 600px; margin: 0 auto;">
    <div class="card-body p-4">
        <form method="POST" action="/users">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="Nhập họ tên...">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" value="{{ old('username') }}" required placeholder="user123...">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" required placeholder="******">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Chức vụ <span class="text-danger">*</span></label>
                <select class="form-select" name="role" required>
                    <option value="">-- Chọn chức vụ --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ __('messages.role_' . $role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Bộ phận phụ trách <span class="text-secondary fw-normal small">(Tùy chọn)</span></label>
                <select class="form-select" name="managed_department">
                    <option value="">-- Chọn bộ phận --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" @selected(old('managed_department') == $dept)>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm tap">
                TẠO NGƯỜI DÙNG
            </button>
        </form>
    </div>
</div>
@endsection
