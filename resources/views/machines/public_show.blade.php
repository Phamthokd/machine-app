@extends('layouts.app-simple')
@section('title', $machine->ma_thiet_bi . ' - Chi tiết')

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
</style>

<!-- Hero Section -->
<div class="hero-card">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="machine-code">{{ $machine->ma_thiet_bi }}</div>
            <div class="machine-name">{{ $machine->ten_thiet_bi }}</div>
            <div class="dept-badge">
                🏢 {{ $machine->department->name ?? 'Chưa gán tổ' }}
            </div>
        </div>
        @role('admin|repair_tech|team_leader')
        <a href="/machines/{{ $machine->id }}/move" class="btn btn-light btn-sm fw-bold text-primary shadow-sm tap" style="border-radius: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Chuyển
        </a>
        @endrole
    </div>
</div>

<!-- Details Grid -->
<div class="info-card">
    <div class="image-placeholder text-center mb-4" style="background: #f1f5f9; padding: 20px; border-radius: 12px; color: #94a3b8;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <div class="small mt-2">Chưa có hình ảnh thiết bị</div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Model</div>
            <div class="info-value">{{ $machine->model ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Hãng</div>
            <div class="info-value">{{ $machine->brand ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Serial</div>
            <div class="info-value">{{ $machine->serial ?? '—' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Năm SX</div>
            <div class="info-value">{{ $machine->year ?? '—' }}</div>
        </div>
        <div class="info-item full-width">
            <div class="info-label">Vị trí / Ghi chú</div>
            <div class="info-value">{{ $machine->vi_tri_text ?? '—' }}</div>
        </div>
        <div class="info-item full-width">
            <div class="info-label">Xuất xứ</div>
            <div class="info-value">{{ $machine->country ?? '—' }}</div>
        </div>
    </div>
</div>

<!-- History Timeline -->
<div class="history-section mb-5" style="padding-bottom: 60px;">
    <div class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Lịch sửa sửa chữa
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
                                    {{ $isDone ? 'Đã xong' : 'Đang sửa' }}
                                </span>
                            </div>
                            <div class="timeline-title">{{ $r->nguyen_nhan }}</div>
                            <div class="timeline-desc">{{ $r->noi_dung_sua_chua }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-muted py-4">
            <div class="mb-2">✨</div>
            Chưa có lịch sử sửa chữa nào
        </div>
    @endif
</div>

<!-- Floating Action Button -->
@role('admin|repair_tech|team_leader|contractor')
<div class="floating-action">
    <a href="/repairs/create?machine={{ $machine->ma_thiet_bi }}" class="btn-create-ticket tap shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        BÁO HỎNG / SỬA MÁY
    </a>
</div>
@endrole

@endsection
