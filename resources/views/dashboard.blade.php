@php
    $maxWidth = '1200px';
@endphp
@extends('layouts.app-simple')
@section('title', __('messages.dashboard'))

@section('content')
<div class="row g-4">
    <!-- Left Column: Profile & Primary Action -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
            <div class="card-header bg-primary text-white p-4 text-center border-0" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
                <div class="avatar rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <h3 class="fw-bold mb-1">{{ Auth::user()->name }}</h3>
                <div class="opacity-75 fs-5">
                    @foreach(Auth::user()->roles as $role)
                        {{ __('messages.role_' . $role->name) }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
            <div class="card-body p-4 d-flex flex-column">
                <div class="p-3 bg-light rounded-3 text-center mb-4">
                    <div class="text-xs text-uppercase text-secondary fw-bold mb-1">{{ __('messages.username') }}</div>
                    <div class="fw-bold text-dark fs-5">{{ Auth::user()->username }}</div>
                </div>

                <div class="mt-auto">
                    <a href="/scan" class="btn btn-primary w-100 py-3 rounded-3 shadow-lg fw-bold d-flex align-items-center justify-content-center gap-2 tap text-uppercase letter-spacing-1" style="font-size: 1.1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
                        {{ __('messages.scan_qr') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Actions Grid -->
    <div class="col-12 col-lg-8">
        <h5 class="fw-bold text-secondary mb-3">{{ __('messages.management_functions') }}</h5>
        
        <div class="row row-cols-2 row-cols-md-3 g-3">
            @hasanyrole('admin|repair_tech|contractor|team_leader|audit')
            <div class="col">
                <a href="/repair-requests" class="btn btn-white border border-danger border-opacity-25 w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <span class="text-danger">{{ __('messages.repair_requests') }}</span>
                </a>
            </div>
            @endhasanyrole

            @hasanyrole('admin|warehouse|repair_tech|audit')
            <div class="col">
                <a href="/repairs" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <span>{{ __('messages.repair_history') }}</span>
                </a>
            </div>
            @endhasanyrole

            @hasanyrole('admin|warehouse|contractor|audit')
            <div class="col">
                <a href="/repairs/contractor" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <span>{{ __('messages.construction_history') }}</span>
                </a>
            </div>
            @endhasanyrole

            @hasanyrole('admin|warehouse|team_leader|audit')
            <div class="col">
                <a href="/movement-history" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <span>{{ __('messages.movement_history') }}</span>
                </a>
            </div>
            @endhasanyrole

            @hasanyrole('admin')
            <div class="col">
                <a href="/users" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span>{{ __('messages.users') }}</span>
                </a>
            </div>
            @endhasanyrole

            @hasanyrole('admin|warehouse|audit')
            <div class="col">
                <a href="/machines" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <span>{{ __('messages.machine_list') }}</span>
                </a>
            </div>
            @endhasanyrole

            @hasanyrole('admin|warehouse')
            <div class="col">
                <a href="/machines/import-csv" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-dark bg-opacity-10 text-dark p-3 rounded-circle mb-3">
                         <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <span>{{ __('messages.import_csv') }}</span>
                </a>
            </div>

            @endhasanyrole

            @hasanyrole('admin|audit')
            <div class="col">
                <a href="/audits" class="btn btn-white border w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-shadow transition">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-3">
                         <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <span>{{ __('messages.internal_audit') }}</span>
                </a>
            </div>
            @endhasanyrole

            <div class="col">
                <form method="POST" action="{{ route('logout') }}" class="h-100">
                    @csrf
                    <button type="submit" class="btn btn-white border border-danger border-opacity-10 w-100 py-4 rounded-4 shadow-sm fw-semibold d-flex flex-column align-items-center justify-content-center h-100 tap hover-danger transition">
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </div>
                        <span class="text-danger">{{ __('messages.logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
