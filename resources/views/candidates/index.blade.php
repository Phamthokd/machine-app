@extends('layouts.app-simple', ['maxWidth' => '1100px'])
@section('title', __('messages.candidates'))

@section('content')
<style>
    .candidate-card {
        border-radius: 1rem;
        transition: all 0.2s;
    }
    /* Mới nộp: Màu xanh lam nhạt */
    .status-card-new {
        background: #f0f9ff !important;
        border: 1.5px solid #bae6fd !important;
    }
    .status-card-new:hover {
        border-color: #7dd3fc !important;
        box-shadow: 0 4px 14px rgba(14,165,233,.12);
    }
    /* Đã chuyển Quản lý: Màu vàng nhạt */
    .status-card-routed {
        background: #fffbeb !important;
        border: 1.5px solid #fde68a !important;
    }
    .status-card-routed:hover {
        border-color: #fcd34d !important;
        box-shadow: 0 4px 14px rgba(245,158,11,.12);
    }
    /* Đã chuyển tiếp: Màu xanh dương */
    .status-card-forwarded {
        background: #eff6ff !important;
        border: 1.5px solid #bfdbfe !important;
    }
    .status-card-forwarded:hover {
        border-color: #93c5fd !important;
        box-shadow: 0 4px 14px rgba(59,130,246,.12);
    }
    /* Không đồng ý tuyển (Đã duyệt): Màu đỏ hồng */
    .status-card-rejected, .status-card-rejected_locked {
        background: #fff1f2 !important;
        border: 1.5px solid #fecdd3 !important;
    }
    .status-card-rejected:hover, .status-card-rejected_locked:hover {
        border-color: #fda4af !important;
        box-shadow: 0 4px 14px rgba(244,63,94,.12);
    }
    /* Không đồng ý tuyển (Chưa duyệt - Bản nháp): Viền đứt nét đỏ */
    .status-card-rejected_draft {
        background: #fffafa !important;
        border: 1.5px dashed #fca5a5 !important;
    }
    .status-card-rejected_draft:hover {
        border-color: #f87171 !important;
        box-shadow: 0 4px 14px rgba(244,63,94,.08);
    }

    /* Đồng ý tuyển (Đã duyệt): Màu xanh lá */
    .status-card-approved, .status-card-approved_locked {
        background: #f0fdf4 !important;
        border: 1.5px solid #bbf7d0 !important;
    }
    .status-card-approved:hover, .status-card-approved_locked:hover {
        border-color: #86efac !important;
        box-shadow: 0 4px 14px rgba(34,197,94,.12);
    }
    /* Đồng ý tuyển (Chưa duyệt - Bản nháp): Viền đứt nét xanh */
    .status-card-approved_draft {
        background: #fbfdfb !important;
        border: 1.5px dashed #86efac !important;
    }
    .status-card-approved_draft:hover {
        border-color: #4ade80 !important;
        box-shadow: 0 4px 14px rgba(34,197,94,.08);
    }

    .avatar-circle {
        width: 44px; height: 44px; border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #2563eb);
        color: white; font-weight: 700; font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .badge-pos { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
    .badge-gender-m { background: #eff6ff; color: #1e40af; }
    .badge-gender-f { background: #fdf2f8; color: #9d174d; }

    .badge-status-new { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
    .badge-status-routed { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
    .badge-status-forwarded { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
    .badge-status-rejected-locked { background: #ffe4e6; color: #9f1239; border: 1.5px solid #f87171; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 700; }
    .badge-status-rejected-draft { background: #fff1f2; color: #be123c; border: 1px dashed #fca5a5; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
    .badge-status-approved-locked { background: #dcfce7; color: #14532d; border: 1.5px solid #4ade80; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 700; }
    .badge-status-approved-draft { background: #f0fdf4; color: #15803d; border: 1px dashed #86efac; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
</style>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('messages.candidates') }}</h2>
        <p class="text-muted small mb-0">{{ __('messages.candidates_subtitle') }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="/slide-huong-dan.html" target="_blank" class="btn btn-outline-info text-dark rounded-3 fw-bold px-3 d-flex align-items-center gap-2" style="background:#f0f9ff; border-color:#7dd3fc;">
            📖 Slide Hướng Dẫn
        </a>
        <a href="/qr-phong-van.html" target="_blank" class="btn btn-outline-primary rounded-3 fw-bold px-3 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            🖨️ In Mã QR Tuyển Dụng
        </a>
        <a href="{{ route('candidates.export', request()->query()) }}" class="btn btn-outline-success rounded-3 fw-bold px-3 d-flex align-items-center gap-2 shadow-sm" title="{{ __('messages.export_excel') ?? 'Xuất Excel' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            📊 {{ __('messages.export_excel') ?? 'Xuất Excel' }}
        </a>
        <a href="{{ route('candidates.create') }}" class="btn btn-primary rounded-3 fw-bold px-4 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('messages.candidate_add') }}
        </a>
    </div>
</div>

{{-- Summary Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
            <div class="text-secondary small fw-medium mb-1">{{ __('messages.candidate_stat_total') }}</div>
            <div class="fs-3 fw-bold text-dark">{{ number_format($stats['total'] ?? 0) }}</div>
            <div class="small text-muted mt-auto pt-1">{{ __('messages.candidate_stat_total_desc') }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-success">
            <div class="text-secondary small fw-medium mb-1">{{ __('messages.candidate_stat_approved') }}</div>
            <div class="fs-3 fw-bold text-success">{{ number_format($stats['total_approved'] ?? 0) }}</div>
            <div class="small text-muted mt-auto pt-1">
                {{ __('messages.candidate_stat_approved_desc', ['rate' => $stats['approval_rate'] ?? 0]) }}
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-danger">
            <div class="text-secondary small fw-medium mb-1">{{ __('messages.candidate_stat_rejected') }}</div>
            <div class="fs-3 fw-bold text-danger">{{ number_format($stats['total_rejected'] ?? 0) }}</div>
            <div class="small text-muted mt-auto pt-1">{{ __('messages.candidate_stat_rejected_desc') }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-warning">
            <div class="text-secondary small fw-medium mb-1">{{ __('messages.candidate_stat_pending') }}</div>
            <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total_pending'] ?? 0) }}</div>
            <div class="small text-muted mt-auto pt-1">{{ __('messages.candidate_stat_pending_desc') }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="card border-0 shadow-sm rounded-3 p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.search') }}</label>
            <input type="text" name="search" class="form-control rounded-3" placeholder="{{ __('messages.search_name_phone_position') }}" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.status') }}</label>
            <select name="status" class="form-select rounded-3">
                <option value="">-- {{ __('messages.all_statuses') }} --</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>🆕 {{ __('messages.candidate_status_new') }}</option>
                <option value="routed" {{ request('status') === 'routed' ? 'selected' : '' }}>⏳ {{ __('messages.candidate_status_routed') }}</option>
                <option value="forwarded" {{ request('status') === 'forwarded' ? 'selected' : '' }}>📤 {{ __('messages.candidate_status_forwarded') }}</option>
                <option value="approved_locked" {{ request('status') === 'approved_locked' ? 'selected' : '' }}>✅ {{ __('messages.candidate_status_approved_locked') }}</option>
                <option value="approved_draft" {{ request('status') === 'approved_draft' ? 'selected' : '' }}>📝 {{ __('messages.candidate_status_approved_draft') }}</option>
                <option value="rejected_locked" {{ request('status') === 'rejected_locked' ? 'selected' : '' }}>❌ {{ __('messages.candidate_status_rejected_locked') }}</option>
                <option value="rejected_draft" {{ request('status') === 'rejected_draft' ? 'selected' : '' }}>📝 {{ __('messages.candidate_status_rejected_draft') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.from_date') }}</label>
            <input type="date" name="start_date" class="form-control rounded-3" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.to_date') }}</label>
            <input type="date" name="end_date" class="form-control rounded-3" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100 rounded-3" title="{{ __('messages.search') ?? 'Tìm kiếm' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
            <a href="{{ route('candidates.index') }}" class="btn btn-light border rounded-3" title="{{ __('messages.7s_v2_btn_reset') ?? 'Đặt lại' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>
</form>

{{-- Chart Report Section --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-3.5 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M18 20V10m-6 10V4m-6 16v-6"/></svg>
                </span>
                <h5 class="fw-bold mb-0 text-dark">{{ __('messages.candidate_chart_title') }}</h5>
            </div>
            <div class="text-muted small mt-1">
                {{ __('messages.candidate_chart_subtitle') }}
                @if(request('start_date') || request('end_date'))
                    <span class="badge bg-light text-secondary border ms-1">
                        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '...' }}
                        →
                        {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '...' }}
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light btn-sm border rounded-3 text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#candidateChartCollapse" aria-expanded="true" aria-controls="candidateChartCollapse" title="{{ __('messages.7s_v2_chart_toggle') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
    </div>

    <div class="collapse show" id="candidateChartCollapse">
        <div class="card-body pt-1 pb-3 px-3 px-md-4">
            @if(($stats['total'] ?? 0) > 0)
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="candidateStatusBarChart"></canvas>
                </div>
                <div class="row g-2 mt-3 pt-2 border-top">
                    <div class="col-6 col-md-3 text-center">
                        <span class="small text-muted">🆕 {{ __('messages.candidate_status_new') }}:</span>
                        <strong class="text-dark ms-1">{{ $stats['status_counts']['new'] ?? 0 }}</strong>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <span class="small text-muted">⏳ {{ __('messages.candidate_stat_in_review') }}:</span>
                        <strong class="text-warning ms-1">{{ ($stats['status_counts']['routed'] ?? 0) + ($stats['status_counts']['forwarded'] ?? 0) }}</strong>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <span class="small text-muted">✅ {{ __('messages.candidate_stat_approved_short') }}:</span>
                        <strong class="text-success ms-1">{{ $stats['total_approved'] ?? 0 }}</strong>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <span class="small text-muted">❌ {{ __('messages.candidate_stat_rejected_short') }}:</span>
                        <strong class="text-danger ms-1">{{ $stats['total_rejected'] ?? 0 }}</strong>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <div class="small">{{ __('messages.candidate_chart_no_data') }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

@forelse($candidates as $c)
@php
    $status = $c->overall_review_status; // 'new', 'routed', 'forwarded', 'approved_locked', 'approved_draft', 'rejected_locked', 'rejected_draft'
@endphp
<div class="candidate-card status-card-{{ $status }} p-3 mb-3 d-flex align-items-center gap-3">
    {{-- Avatar --}}
    <div class="avatar-circle">
        @if($c->photo_path)
            <img src="/{{ $c->photo_path }}" alt="{{ $c->full_name }}">
        @else
            {{ mb_substr($c->full_name, 0, 1) }}
        @endif
    </div>
    {{-- Info --}}
    <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold text-dark">{{ $c->full_name }}</span>
            <span class="badge {{ $c->gender === 'male' ? 'badge-gender-m' : 'badge-gender-f' }}">
                {{ $c->gender === 'male' ? '♂ ' . __('messages.gender_male') : '♀ ' . __('messages.gender_female') }}
            </span>
            @if($status === 'approved_locked')
                <span class="badge badge-status-approved-locked">✅ {{ __('messages.candidate_status_approved_locked') }}</span>
            @elseif($status === 'approved_draft')
                <span class="badge badge-status-approved-draft">📝 {{ __('messages.candidate_status_approved_draft') }}</span>
            @elseif($status === 'rejected_locked')
                <span class="badge badge-status-rejected-locked">❌ {{ __('messages.candidate_status_rejected_locked') }}</span>
            @elseif($status === 'rejected_draft')
                <span class="badge badge-status-rejected-draft">📝 {{ __('messages.candidate_status_rejected_draft') }}</span>
            @elseif($status === 'forwarded')
                <span class="badge badge-status-forwarded">📤 {{ __('messages.candidate_status_forwarded') }}</span>
            @elseif($status === 'routed')
                <span class="badge badge-status-routed">⏳ {{ __('messages.candidate_status_routed') }}</span>
            @else
                <span class="badge badge-status-new">🆕 {{ __('messages.candidate_status_new') }}</span>
            @endif

            @if($c->seniorManagers->isNotEmpty() && in_array($status, ['routed', 'forwarded', 'approved_draft', 'rejected_draft']))
                <span class="text-secondary small fw-medium" style="font-size:.78rem;">
                    ({{ __('messages.reviewing_by') }} {{ $c->seniorManagers->pluck('name')->join(', ') }})
                </span>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2 mt-1">
            @if($c->department_applied)
            <span class="badge bg-light text-secondary border">🏢 {{ $c->department_applied }}</span>
            @endif
            <span class="badge-pos">{{ $c->position_applied }}</span>
            <span class="text-muted small">📞 {{ $c->phone }}</span>
            @if($c->dob)
            <span class="text-muted small">🎂 {{ $c->dob->format('d/m/Y') }}</span>
            @endif
        </div>
    </div>
    {{-- Date + Actions --}}
    <div class="text-end flex-shrink-0">
        <div class="text-muted small mb-2">{{ $c->created_at->format('d/m/Y') }}</div>
        <div class="d-flex gap-1 justify-content-end">
            <a href="{{ route('candidates.show', $c->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-2" title="Xem chi tiết">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            @php
                $canEditCard = auth()->user()->hasAnyRole(['admin', 'hr']) ||
                    ($c->seniorManagers->isEmpty() && $c->created_at >= now()->subMinutes(30));
            @endphp
            @if($canEditCard)
            <a href="{{ route('candidates.edit', $c->id) }}" class="btn btn-sm btn-outline-warning text-dark border-warning rounded-2 px-2" title="Chỉnh sửa">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            @endif
            <a href="{{ route('candidates.print', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-2 px-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            </a>
            @if(auth()->user()->isAdminUser())
            <form action="{{ route('candidates.destroy', $c->id) }}" method="POST"
                  onsubmit="return confirm('{{ __('messages.candidate_delete_confirm') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    <div class="text-muted mt-3">{{ __('messages.no_candidates') }}</div>
    <a href="{{ route('candidates.create') }}" class="btn btn-primary rounded-3 mt-3">{{ __('messages.candidate_add') }}</a>
</div>
@endforelse

{{ $candidates->links() }}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('candidateStatusBarChart');
    if (!canvas) return;

    const stats = @json($stats['status_counts'] ?? []);
    
    // Status definitions & colors
    const items = [
        { key: 'new', label: '🆕 {{ __("messages.candidate_status_new") }}', color: '#38bdf8', border: '#0284c7' },
        { key: 'routed', label: '⏳ {{ __("messages.candidate_status_routed") }}', color: '#fbbf24', border: '#d97706' },
        { key: 'forwarded', label: '📤 {{ __("messages.candidate_status_forwarded") }}', color: '#60a5fa', border: '#2563eb' },
        { key: 'approved_draft', label: '📝 {{ __("messages.candidate_status_approved_draft") }}', color: '#86efac', border: '#16a34a' },
        { key: 'approved_locked', label: '✅ {{ __("messages.candidate_status_approved_locked") }}', color: '#22c55e', border: '#15803d' },
        { key: 'rejected_draft', label: '📝 {{ __("messages.candidate_status_rejected_draft") }}', color: '#fca5a5', border: '#dc2626' },
        { key: 'rejected_locked', label: '❌ {{ __("messages.candidate_status_rejected_locked") }}', color: '#ef4444', border: '#b91c1c' },
    ];

    const labels = items.map(i => i.label);
    const data = items.map(i => stats[i.key] || 0);
    const bgColors = items.map(i => i.color);
    const borderColors = items.map(i => i.border);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '{{ __("messages.candidate_chart_dataset_label") }}',
                data: data,
                backgroundColor: bgColors,
                borderColor: borderColors,
                borderWidth: 1.5,
                borderRadius: 6,
                barPercentage: 0.65,
                categoryPercentage: 0.75,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 13, weight: '700' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            const val = context.raw || 0;
                            return ` Số lượng: ${val} phiếu`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 11, weight: '500' },
                        color: '#475569',
                        maxRotation: 25,
                        minRotation: 0,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: {
                        precision: 0,
                        font: { family: "'Inter', sans-serif", size: 11 },
                        color: '#64748b'
                    }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
