@extends('layouts.app-simple')
@section('title', __('messages.edit_user'))

@php
    $selectedPermissions = old('permissions', $user->permissions->pluck('name')->all());
@endphp

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/users" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('messages.back') }}
    </a>
    <h4 class="mb-0 fw-bold">{{ __('messages.edit_user_name') }} {{ $user->name }}</h4>
</div>

<div class="card border-0 shadow-sm rounded-4" style="max-width: 860px; margin: 0 auto;">
    <div class="card-body p-4">
        <form method="POST" action="/users/{{ $user->id }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('messages.username') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" value="{{ old('username', $user->username) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('messages.new_password') }}</label>
                <input type="password" class="form-control" name="password" placeholder="{{ __('messages.leave_blank_password') }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('messages.role') }} <span class="text-danger">*</span></label>
                <select class="form-select" name="role" required>
                    <option value="">{{ __('messages.select_role') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                            {{ __('messages.role_' . $role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">{{ __('messages.managed_department') }} <span class="text-secondary fw-normal small">({{ mb_strtolower(__('messages.optional')) }})</span></label>
                <select class="form-select" name="managed_department">
                    <option value="">{{ __('messages.select_department') }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" @selected(old('managed_department', $user->managed_department) == $dept)>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold d-block">{{ __('messages.status') }}</label>
                <label class="form-check d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
                    <span>{{ __('messages.account_is_active') }}</span>
                </label>
                @if(auth()->id() === $user->id)
                    <div class="form-text">{{ __('messages.cannot_self_deactivate_warn') }}</div>
                @endif
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">{{ __('messages.authorized_functions') }}</label>
                <div class="text-muted small mb-3">{{ __('messages.permission_hint') }}</div>
                <div class="row g-3">
                    @foreach($permissionGroups as $group)
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                                <div class="fw-bold mb-3">{{ __($group['group']) }}</div>
                                @foreach($group['items'] as $permission => $label)
                                    <label class="form-check d-flex align-items-start gap-2 mb-2">
                                        <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="{{ $permission }}" @checked(in_array($permission, $selectedPermissions))>
                                        <span>{{ __($label) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm tap">
                {{ mb_strtoupper(__('messages.update')) }}
            </button>
        </form>
    </div>
</div>
@endsection
