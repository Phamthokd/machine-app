@php
    $maxWidth = '1000px';
@endphp
@extends('layouts.app-simple')
@section('title', __('messages.users_management'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
    <h4 class="mb-0 fw-bold">{{ __('messages.users_list') }}</h4>
    <div class="d-flex gap-2 w-100 w-md-auto">
        <form class="d-flex gap-2 flex-grow-1" method="GET" action="/users">
            <div class="input-group shadow-sm">
                <input type="text" class="form-control border-0 bg-light" name="search"
                    placeholder="{{ __('messages.search_users_placeholder') }}"
                    value="{{ $search ?? '' }}">
                <button class="btn btn-primary px-3" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
                @if($search ?? false)
                    <a href="/users" class="btn btn-outline-secondary border-0 bg-light">✕</a>
                @endif
            </div>
        </form>
        @role('admin')
        <a href="/users/create" class="btn btn-primary shadow-sm tap d-flex align-items-center gap-2 text-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>{{ __('messages.add_new') }}</span>
        </a>
        @endrole
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-4 text-secondary text-xs uppercase font-weight-bolder opacity-7">{{ __('messages.full_name') }}</th>
                    <th class="py-3 text-secondary text-xs uppercase font-weight-bolder opacity-7">{{ __('messages.username') }}</th>
                    <th class="py-3 text-secondary text-xs uppercase font-weight-bolder opacity-7">{{ mb_strtoupper(__('messages.department')) }}</th>
                    <th class="py-3 text-secondary text-xs uppercase font-weight-bolder opacity-7">{{ __('messages.role') }}</th>
                    <th class="py-3 text-end pe-4 text-secondary text-xs uppercase font-weight-bolder opacity-7">{{ __('messages.actions') }}</th>
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
                        @if($user->managed_department)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $user->managed_department }}</span>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-info bg-opacity-10 text-info">{{ __('messages.role_' . $role->name) }}</span>
                        @endforeach
                    </td>
                    <td class="text-end pe-4">
                        <a href="/users/{{ $user->id }}/edit" class="btn btn-sm btn-light text-primary fw-semibold">
                            {{ __('messages.edit_btn') }}
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
