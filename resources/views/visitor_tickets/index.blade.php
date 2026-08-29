@php
    $maxWidth = '100%';
@endphp
@extends('layouts.app-simple')
@section('title', 'Phiếu Đăng Ký Khách')

@section('content')
@php $currentUser = auth()->user(); @endphp

<div class="d-flex align-items-center justify-content-between mb-3 mb-md-4 gap-2 flex-wrap">
    <div>
        <h4 class="fw-bold mb-1 fs-5 fs-md-4 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Phiếu Đăng Ký Khách
        </h4>
        <div class="text-muted small d-none d-sm-block">Danh sách phiếu đăng ký khách vào công ty</div>
    </div>

    @if(\App\Support\FeatureAccess::allows($currentUser, 'visitors.create'))
    <a href="{{ route('visitor-tickets.create') }}" class="btn btn-primary btn-sm btn-md d-flex align-items-center gap-2 rounded-pill px-3 py-2 shadow-sm fw-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Tạo Phiếu Mới</span>
    </a>
    @endif
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('visitor-tickets.index') }}" class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold mb-1">Tìm kiếm</label>
                <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Đơn vị, mục đích, tên khách...">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Ngày</label>
                <input type="date" class="form-control form-control-sm" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Trạng thái</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">Tất cả</option>
                    <option value="open" @selected(request('status') === 'open')>Đang mở</option>
                    <option value="closed" @selected(request('status') === 'closed')>Đã đóng</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill rounded-3 py-2 fw-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Lọc
                </button>
                @if(request()->hasAny(['search','date','status']))
                <a href="{{ route('visitor-tickets.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 py-2 px-3">✕</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Table Desktop / Card Mobile --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        @if($tickets->isEmpty())
            <div class="text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <div class="fw-semibold">Chưa có phiếu nào</div>
                <div class="small">Tạo phiếu mới để bắt đầu</div>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="small text-uppercase text-secondary fw-semibold">
                            <th class="px-4 py-3" style="width:60px">STT</th>
                            <th class="py-3">Đơn vị khách</th>
                            <th class="py-3">Mục đích</th>
                            <th class="py-3">Ngày</th>
                            <th class="py-3 text-center">Số khách</th>
                            <th class="py-3 text-center">Trạng thái</th>
                            <th class="py-3">Người tạo</th>
                            <th class="py-3 pe-4 text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr class="border-bottom">
                            <td class="px-4 text-muted small">{{ $loop->iteration + ($tickets->currentPage() - 1) * $tickets->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ticket->guest_unit }}</div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width:260px" title="{{ $ticket->purpose }}">{{ $ticket->purpose }}</div>
                            </td>
                            <td class="small text-muted">{{ $ticket->visit_date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                    {{ $ticket->guests->count() }} người
                                </span>
                            </td>
                            <td class="text-center">
                                @if($ticket->isOpen())
                                    <span class="badge rounded-pill" style="background:#dcfce7;color:#15803d;font-size:0.78rem;">
                                        ● Đang mở
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background:#f1f5f9;color:#64748b;font-size:0.78rem;">
                                        ✓ Đã đóng
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $ticket->creator->name ?? '—' }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('visitor-tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Xem
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="d-block d-lg-none p-3">
                <div class="d-flex flex-column gap-3">
                    @foreach($tickets as $ticket)
                    <div class="card border rounded-4 shadow-sm overflow-hidden">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-secondary small fw-semibold">#{{ $ticket->id }}</span>
                                    <h6 class="fw-bold mb-0 text-dark mt-1">{{ $ticket->guest_unit }}</h6>
                                </div>
                                <div>
                                    @if($ticket->isOpen())
                                        <span class="badge rounded-pill px-2 py-1" style="background:#dcfce7;color:#15803d;font-size:0.78rem;">
                                            ● Đang mở
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2 py-1" style="background:#f1f5f9;color:#64748b;font-size:0.78rem;">
                                            ✓ Đã đóng
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-secondary small mb-3 text-truncate" style="max-width: 100%;">
                                <span class="fw-semibold text-dark">Mục đích:</span> {{ $ticket->purpose }}
                            </p>

                            <div class="bg-light rounded-3 p-2 d-flex justify-content-between text-muted small mb-3">
                                <div>📅 {{ $ticket->visit_date->format('d/m/Y') }}</div>
                                <div>👥 <span class="fw-bold text-dark">{{ $ticket->guests->count() }}</span> khách</div>
                                <div>👤 {{ $ticket->creator->name ?? '—' }}</div>
                            </div>

                            <a href="{{ route('visitor-tickets.show', $ticket->id) }}" class="btn btn-primary w-100 rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 tap">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                CHI TIẾT PHIẾU
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($tickets->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $tickets->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
