@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.machine_list_title'))

@section('content')
<div class="container-fluid px-4">
    <!-- Header & Search -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-4">
                <div class="d-flex align-items-center gap-3 w-100 w-lg-auto">
                    <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ __('messages.machine_list_title') }}</h4>
                        <div class="text-secondary small">{{ __('messages.machine_list_subtitle') }}</div>
                    </div>
                </div>
                
                <form class="d-flex flex-column flex-md-row gap-3 w-100 w-lg-auto" method="GET">
                    <select class="form-select form-select-lg fs-6 shadow-sm border-0 bg-light w-100 w-md-auto" name="department_id" onchange="this.form.submit()" style="min-width: 200px;">
                        <option value="">{{ __('messages.all_departments') }}</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" @selected(request('department_id') == $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group shadow-sm border-0 rounded-3 overflow-hidden w-100 w-md-auto">
                        <input type="text" class="form-control form-control-lg fs-6 border-0 bg-light" name="search" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}" style="min-width: 200px; width: 300px; max-width: 100%;">
                        <button class="btn btn-primary px-4" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(request('department_id'))
        <div class="mb-4 d-flex justify-content-end">
            <a href="{{ route('machines.print_department_qr', request('department_id')) }}" target="_blank" class="btn btn-dark d-flex align-items-center gap-2 shadow-sm tap">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                {{ __('messages.print_batch_qr') }}
            </a>
        </div>
    @endif

    <!-- List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped align-middle mb-0 text-nowrap" style="min-width: 100%; font-size: 0.85rem;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="py-2 px-2">{{ __('messages.machine_code') }}</th>
                        <th class="py-2 px-2">{{ __('messages.machine_name') }}</th>
                        <th class="py-2 px-2">{{ __('messages.current_department') }}</th>
                        <th class="py-2 px-2">{{ __('messages.brand') }}</th>
                        <th class="py-2 px-2">{{ __('messages.model') }}</th>
                        <th class="py-2 px-2">{{ __('messages.serial') }}</th>
                        <th class="py-2 px-2">{{ __('messages.invoice_cd') }}</th>
                        <th class="py-2 px-2">{{ __('messages.year') }}</th>
                        <th class="py-2 px-2">{{ __('messages.country') }}</th>
                        <th class="py-2 px-2">{{ __('messages.stock_in_date') }}</th>
                        <th class="py-2 px-2">{{ __('messages.location_txt') }}</th>
                        <th class="py-2 px-2">{{ __('messages.warehouse_in_date') }}</th>
                        <th class="py-2 px-2">{{ __('messages.warehouse_out_date') }}</th>
                        <th class="py-2 px-2 text-center" style="width: 80px;">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($machines as $m)
                    <tr>
                        <td class="px-2 fw-bold text-primary">
                            <a href="/m/{{ $m->ma_thiet_bi }}" class="text-decoration-none">{{ $m->ma_thiet_bi }}</a>
                        </td>
                        <td class="px-2 text-truncate" style="max-width: 200px;" title="{{ $m->ten_thiet_bi }}">{{ $m->ten_thiet_bi }}</td>
                        <td class="px-2">{{ $m->department->name ?? '-' }}</td>
                        <td class="px-2">{{ $m->brand }}</td>
                        <td class="px-2">{{ $m->model }}</td>
                        <td class="px-2 text-secondary">{{ $m->serial }}</td>
                        <td class="px-2">{{ $m->invoice_cd }}</td>
                        <td class="px-2">{{ $m->year }}</td>
                        <td class="px-2">{{ $m->country }}</td>
                        <td class="px-2">{{ $m->stock_in_date ? \Carbon\Carbon::parse($m->stock_in_date)->format('d/m/Y') : '' }}</td>
                        <td class="px-2">{{ $m->vi_tri_text }}</td>
                        <td class="px-2">{{ $m->ngay_vao_kho ? \Carbon\Carbon::parse($m->ngay_vao_kho)->format('d/m/Y') : '' }}</td>
                        <td class="px-2">{{ $m->ngay_ra_kho ? \Carbon\Carbon::parse($m->ngay_ra_kho)->format('d/m/Y') : '' }}</td>
                        <td class="px-2 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('machines.print_qr', $m->id) }}" target="_blank" class="btn btn-sm btn-outline-dark border-0 p-1" title="{{ __('messages.print_qr_action') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                </a>
                                <a href="{{ route('machines.edit', $m->id) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="{{ __('messages.edit') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="text-center py-5 text-secondary">
                            {{ __('messages.no_data_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($machines->hasPages())
        <div class="px-3 py-2 border-top bg-white">
            {{ $machines->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
