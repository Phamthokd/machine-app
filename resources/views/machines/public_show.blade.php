@extends('layouts.app-simple')
@section('title', $machine->ma_thiet_bi . ' - ' . __('messages.machine_detail_title'))

@section('content')
<style>
    :root {
        --primary-color: #4f46e5;
        --secondary-color: #64748b;
        --bg-surface: #ffffff;
        --bg-app: #f8fafc;
    }
    body {
        background-color: var(--bg-app) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    /* Hero Section */
    .hero-card {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        position: relative;
        overflow: hidden;
    }
    .hero-card::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .machine-code {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        margin-bottom: 4px;
    }
    .machine-name {
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 500;
    }
    .dept-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 12px;
    }

    /* Info Card */
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 20px; /* Thêm padding dưới để nút không sát lề */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        padding-bottom: 80px; /* Chừa chỗ cho nút fixed nếu cần, hoặc nút thường */
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .info-item {
        margin-bottom: 8px;
    }
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 2px;
        font-weight: 600;
    }
    .info-value {
        font-size: 0.95rem;
        color: #334155;
        font-weight: 600;
        word-break: break-word;
    }
    .full-width {
        grid-column: span 2;
    }

    /* Timeline Section */
    .history-section {
        margin-top: 24px;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .timeline {
        position: relative;
        padding-left: 24px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
        border-radius: 2px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 24px;
    }
    .timeline-dot {
        position: absolute;
        left: -24px;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        border: 3px solid #cbd5e1;
        z-index: 1;
    }
    .timeline-item.active .timeline-dot {
        border-color: #ef4444; /* Red for active/broken */
    }
    .timeline-item.done .timeline-dot {
        border-color: #10b981; /* Green for done */
    }
    
    .timeline-card {
        background: white;
        border-radius: 12px;
        padding: 12px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s;
    }
    .timeline-card:active {
        transform: scale(0.98);
    }
    .timeline-date {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 4px;
    }
    .timeline-title {
        font-weight: 600;
        color: #334155;
        margin-bottom: 4px;
    }
    .timeline-desc {
        font-size: 0.85rem;
        color: #475569;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Sticky Action Button */
    .floating-action {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 600px;
        z-index: 100;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
    .btn-create-ticket {
        background: #ef4444;
        color: white;
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-create-ticket:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }
    .btn-create-ticket:active {
        transform: translateY(0);
    }

    /* Premium Option Cards inside Modal */
    .breakdown-option-card {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .breakdown-option-card:hover {
        transform: translateY(-3px);
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08) !important;
        background: #ffffff !important;
    }
    .breakdown-option-card:active {
        transform: scale(0.98);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
    }
    .breakdown-option-card:hover .arrow-box {
        color: var(--primary-color) !important;
        transform: translateX(3px);
    }
    .arrow-box {
        transition: all 0.2s;
    }
</style>

<!-- Hero Section -->
<div class="hero-card">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="machine-code">{{ $machine->ma_thiet_bi }}</div>
            <div class="machine-name">{{ $machine->ten_thiet_bi }}</div>
            <div class="dept-badge">
                🏢 {{ __('messages.hero_card_dept_prefix') }}: {{ $machine->department->name ?? __('messages.not_assigned') }}
            </div>
        </div>
        <div class="d-flex gap-2" style="position: relative; z-index: 10;">
            @if(auth()->user()->canManageRepairs())
            <a href="/repairs/create?machine={{ $machine->ma_thiet_bi }}&type=maintenance" class="btn btn-success btn-sm fw-bold shadow-sm tap" style="border-radius: 8px;">
                {{ __('messages.maintenance') }}
            </a>
            @endif
            @if(auth()->user()->canMoveMachines())
            <a href="/machines/{{ $machine->id }}/move" class="btn btn-light btn-sm fw-bold text-primary shadow-sm tap" style="border-radius: 8px;">
                {{ __('messages.move_action') }}
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Details Grid -->
<div class="info-card">
    <div class="image-placeholder text-center mb-4" style="background: #f1f5f9; padding: 20px; border-radius: 12px; color: #94a3b8;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <div class="small mt-2">{{ __('messages.no_image') }}</div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">{{ __('messages.model') }}</div>
            <div class="info-value">{{ $machine->model ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">{{ __('messages.brand') }}</div>
            <div class="info-value">{{ $machine->brand ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">{{ __('messages.serial') }}</div>
            <div class="info-value">{{ $machine->serial ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">{{ __('messages.year_produced') }}</div>
            <div class="info-value">{{ $machine->year ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">{{ __('messages.warranty_period') }}</div>
            <div class="info-value">{{ $machine->warranty_period ?? '—' }}</div>
        </div>
        <div class="info-item full-width">
            <div class="info-label">{{ __('messages.location_note') }}</div>
            <div class="info-value">{{ $machine->vi_tri_text ?? '—' }}</div>
        </div>
        <div class="info-item full-width">
            <div class="info-label">{{ __('messages.origin') }}</div>
            <div class="info-value">{{ $machine->country ?? '—' }}</div>
        </div>
    </div>
</div>

<!-- History Timeline -->
<div class="history-section mb-5" style="padding-bottom: 60px;">
    <div class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        {{ __('messages.repair_history_title') }}
    </div>

    @if($machine->repairTickets && $machine->repairTickets->count())
        <div class="timeline">
            @foreach($machine->repairTickets as $r)
                @php
                    $isDone = !empty($r->ended_at);
                    $statusClass = $isDone ? 'done' : 'active';
                @endphp
                <div class="timeline-item {{ $statusClass }}">
                    <div class="timeline-dot"></div>
                    <a href="/repairs/{{ $r->id }}" class="text-decoration-none">
                        <div class="timeline-card">
                            <div class="d-flex justify-content-between">
                                <span class="timeline-date">{{ $r->created_at->format('d/m/Y H:i') }}</span>
                                <span class="badge {{ $isDone ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill" style="font-size: 0.7rem;">
                                    {{ $isDone ? __('messages.status_done') : __('messages.status_repairing') }}
                                </span>
                            </div>
                            <div class="timeline-title">{{ $r->nguyen_nhan }}</div>
                            <div class="timeline-desc">{{ $r->noi_dung_sua_chua }}</div>
                            
                            @if($isDone)
                                @if(auth()->user()->isAdminUser())
                                <div class="mt-2 text-end">
                                    <object><a href="{{ route('repairs.edit_completed', $r->id) }}" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem; border-radius: 6px; padding: 2px 8px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        {{ __('messages.edit_admin') }}
                                    </a></object>
                                </div>
                                @endif
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-muted py-4">
            <div class="mb-2">✨</div>
            <div class="mb-2">✨</div>
            {{ __('messages.no_history') }}
        </div>
    @endif
</div>

<!-- Floating Action Button -->
@if(auth()->user()->canManageRepairs())
@php
    $hasPending = $machine->repairTickets->contains(function ($t) {
        return empty($t->ended_at);
    });
@endphp

@if($hasPending)
<div class="floating-action">
    <button class="btn-create-ticket shadow-lg" style="background: #fbbf24; color: #78350f; cursor: not-allowed; border: 1px solid #f59e0b;" disabled>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        {{ __('messages.reported_waiting_repair') }}
    </button>
</div>
@else
<div class="floating-action">
    <button type="button" class="btn-create-ticket tap shadow-lg" data-bs-toggle="modal" data-bs-target="#breakdownTypeModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        {{ __('messages.report_issue_btn') }}
    </button>
</div>
@endif
@endif

@endsection

@push('modals')
<!-- Breakdown Type Selection Modal -->
<div class="modal fade" id="breakdownTypeModal" tabindex="-1" aria-labelledby="breakdownTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: #ffffff;">
            <!-- Modal Header with premium design -->
            <div class="modal-header border-0 text-center d-flex flex-column align-items-center pt-4 pb-2" style="position: relative;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 20px; top: 20px; filter: grayscale(1); opacity: 0.6; transition: all 0.2s; border: none; background: transparent;"></button>
                <div class="mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(79, 70, 229, 0.08); border-radius: 50%; color: #4f46e5;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <h5 class="modal-title fw-bold text-dark px-3 text-center" id="breakdownTypeModalLabel" style="font-size: 1.25rem;">
                    {{ app()->getLocale() == 'vi' ? 'Chọn loại sự cố báo hỏng' : (app()->getLocale() == 'zh' ? '选择报修故障类型' : 'Select breakdown type') }}
                </h5>
                <p class="text-muted small px-4 mt-1 mb-0 text-center">
                    {{ app()->getLocale() == 'vi' ? 'Vui lòng chọn loại dịch vụ sửa chữa phù hợp để kỹ thuật viên hỗ trợ nhanh nhất' : (app()->getLocale() == 'zh' ? '请选择合适的维修服务，以便技术人员尽快提供支持' : 'Please select the appropriate repair service for the fastest technical support') }}
                </p>
            </div>
            
            <!-- Modal Body with Premium Stacked Gradient Buttons -->
            <div class="modal-body p-4 d-flex flex-column gap-3">
                <!-- Option 1: Call Repair Tech -->
                <a href="/repairs/create?machine={{ $machine->ma_thiet_bi }}&type=mechanic" class="breakdown-option-card d-flex align-items-center p-3 text-decoration-none shadow-sm transition-all" style="border-radius: 16px; border: 1.5px solid #eef2f6; background: linear-gradient(135deg, #ffffff 0%, #fbfcfe 100%);">
                    <div class="option-icon-box d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 12px; color: white; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25); flex-shrink: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <div class="flex-grow-1">
                        <div class="option-title fw-bold text-dark" style="font-size: 1.05rem;">
                            {{ __('messages.type_repair') }} ({{ app()->getLocale() == 'vi' ? 'Cơ điện' : (app()->getLocale() == 'zh' ? '机电' : 'Mechanic') }})
                        </div>
                        <div class="option-desc text-muted small mt-1" style="font-size: 0.82rem; line-height: 1.3;">
                            {{ app()->getLocale() == 'vi' ? 'Báo hỏng máy móc thiết bị, cơ khí, sự cố điện máy...' : (app()->getLocale() == 'zh' ? '报告机器设备、机械、电机故障...' : 'Report machine, mechanical, or electrical issues...') }}
                        </div>
                    </div>
                    <div class="arrow-box ms-2 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </a>
                
                <!-- Option 2: Call Contractor -->
                <a href="/repairs/create?machine={{ $machine->ma_thiet_bi }}&type=contractor" class="breakdown-option-card d-flex align-items-center p-3 text-decoration-none shadow-sm transition-all" style="border-radius: 16px; border: 1.5px solid #eef2f6; background: linear-gradient(135deg, #ffffff 0%, #fbfcfe 100%);">
                    <div class="option-icon-box d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); border-radius: 12px; color: white; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.25); flex-shrink: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                            <line x1="15" y1="3" x2="15" y2="21"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="3" y1="15" x2="21" y2="15"/>
                        </svg>
                    </div>
                    <div class="flex-grow-1">
                        <div class="option-title fw-bold text-dark" style="font-size: 1.05rem;">
                            {{ __('messages.type_construction') }} ({{ app()->getLocale() == 'vi' ? 'Thầu phụ' : (app()->getLocale() == 'zh' ? '承包商' : 'Contractor') }})
                        </div>
                        <div class="option-desc text-muted small mt-1" style="font-size: 0.82rem; line-height: 1.3;">
                            {{ app()->getLocale() == 'vi' ? 'Báo lỗi điện nước, nhà xưởng, cơ sở hạ tầng, xây dựng...' : (app()->getLocale() == 'zh' ? '报告水电、厂房、基础设施、建筑故障...' : 'Report water, electricity, workshop, facilities, civil issues...') }}
                        </div>
                    </div>
                    <div class="arrow-box ms-2 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </a>
            </div>
            
            <div class="modal-footer border-0 p-3 bg-light bg-opacity-50 text-center justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm px-4 py-2" data-bs-dismiss="modal" style="border-radius: 20px; font-weight: 600; border: none; background: #e2e8f0; color: #475569;">
                    {{ app()->getLocale() == 'vi' ? 'Đóng' : (app()->getLocale() == 'zh' ? '关闭' : 'Close') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

