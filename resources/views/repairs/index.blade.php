@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title','Danh sách phiếu sửa')

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
    .page-header {
        margin-bottom: 1.5rem;
    }
    .page-title {
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.5px;
    }
    .card-modern {
        background: var(--bg-surface);
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    .table-responsive {
        max-height: 80vh;
        overflow-y: auto;
    }
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
    }
    .modern-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.925rem;
        color: #334155;
    }
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    .modern-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .badge-dept {
        background: #e0e7ff;
        color: #4338ca;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
    }
    .text-link {
        color: var(--primary-color);
        font-weight: 500;
        text-decoration: none;
    }
    .text-link:hover {
        text-decoration: underline;
    }
    .col-min-150 { min-width: 150px; }
    .col-min-200 { min-width: 200px; }
    .btn-export {
        background-color: #10b981;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-export:hover {
        background-color: #059669;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    
    /* Mobile Card Styles */
    .repair-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .repair-card .card-header-line {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .repair-card .machine-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.1rem;
    }
    .repair-card .dept-tag {
        font-size: 0.75rem;
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .repair-card .info-row {
        display: flex;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }
    .repair-card .info-label {
        color: #64748b;
        width: 80px;
        flex-shrink: 0;
    }
    .repair-card .info-value {
        color: #334155;
        font-weight: 500;
    }
    .repair-card .divider {
        height: 1px;
        background: #f1f5f9;
        margin: 12px 0;
    }
    .repair-card .footer-info {
        display: flex;
        gap: 12px;
        font-size: 0.8rem;
        color: #64748b;
        flex-wrap: wrap;
    }
</style>

<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div>
        <h2 class="page-title h3 mb-1">📋 Danh sách phiếu sửa</h2>
        <div class="text-muted">Quản lý và theo dõi lịch sử sửa chữa thiết bị</div>
    </div>
    <a href="/repairs/export" class="btn-export text-decoration-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span>Xuất Excel</span>
    </a>
</div>

<div class="card-modern d-none d-md-block">
    <div class="table-responsive">
        <table class="modern-table mb-0">
            <thead>
                <tr>
                    <th class="text-center" width="50">#</th>
                    <th>Thiết bị</th>
                    <th>Tổ</th>
                    <th>Thông tin hàng</th>
                    <th class="col-min-200">Vấn đề</th>
                    <th class="col-min-200">Khắc phục</th>
                    <th>Thời gian</th>
                    <th>Nhân sự liên quan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repairs as $r)
                <tr>
                    <td class="text-center text-muted small">{{ $r->id }}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <a class="text-link fw-bold" href="/m/{{ $r->machine->ma_thiet_bi }}">
                                {{ $r->machine->ma_thiet_bi }}
                            </a>
                            <span class="small text-muted">{{ $r->machine->ten_thiet_bi }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge-dept">{{ $r->machine->department->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="small">
                            <div><span class="text-muted">Mã:</span> <span class="fw-medium">{{ $r->ma_hang }}</span></div>
                            <div><span class="text-muted">CĐ:</span> {{ $r->cong_doan }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="text-break">{{ $r->nguyen_nhan }}</div>
                    </td>
                    <td>
                        <div class="text-break">{{ $r->noi_dung_sua_chua }}</div>
                    </td>
                    <td>
                        <div class="d-flex flex-column small" style="white-space: nowrap;">
                            <span class="text-secondary" title="Thời gian báo hỏng">Report: {{ $r->created_at->format('H:i d/m') }}</span>
                            
                            @if($r->started_at)
                                <span class="text-success" title="Thời gian tiếp nhận">Start: &nbsp;&nbsp;{{ \Carbon\Carbon::parse($r->started_at)->format('H:i d/m') }}</span>
                                
                                @php
                                    $waitTime = $r->created_at->diffInMinutes(\Carbon\Carbon::parse($r->started_at));
                                @endphp
                                <span class="badge bg-light text-dark border mt-1" title="Thời gian chờ từ lúc báo đến lúc tiếp nhận">Wait: {{ $waitTime }} min</span>
                            @endif

                            @if($r->ended_at)
                                <span class="text-secondary mt-1" title="Thời gian hoàn thành">End: &nbsp;&nbsp;&nbsp;&nbsp;{{ \Carbon\Carbon::parse($r->ended_at)->format('H:i d/m') }}</span>
                            @else
                                <span class="badge bg-warning text-dark mt-1">Đang sửa</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1 small">
                            <div title="Người tạo">🛠 {{ $r->createdBy->name ?? '...' }}</div>
                            <div class="text-muted" title="Inline QC">👀 {{ $r->inlineQc->name ?? '—' }} (QC)</div>
                            <div class="text-muted" title="Endline QC">check {{ $r->endlineQc->name ?? '—' }} (Endline)</div>
                            <div class="text-muted" title="QA Chủ quản">recheck {{ $r->qaSupervisor->name ?? '—' }} (QA)</div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="d-md-none">
    @foreach($repairs as $r)
    <div class="repair-card">
        <div class="card-header-line">
            <div>
                <a class="machine-name text-decoration-none" href="/m/{{ $r->machine->ma_thiet_bi }}">
                    {{ $r->machine->ma_thiet_bi }}
                </a>
                <div class="small text-muted">{{ $r->machine->ten_thiet_bi }}</div>
            </div>
            <span class="badge-dept">{{ $r->machine->department->name ?? 'N/A' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Mã hàng:</span>
            <span class="info-value">{{ $r->ma_hang }} ({{ $r->cong_doan }})</span>
        </div>

        <div class="info-row">
            <span class="info-label">Ng.nhân:</span>
            <span class="info-value text-danger">{{ $r->nguyen_nhan }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">K.phục:</span>
            <span class="info-value text-success">{{ $r->noi_dung_sua_chua }}</span>
        </div>

        <div class="divider"></div>

        <div class="d-flex justify-content-between align-items-end">
            <div class="footer-info">
                <div>🕒 {{ \Carbon\Carbon::parse($r->started_at)->format('H:i d/m') }}</div>
                <div>👤 {{ $r->createdBy->name ?? '...' }}</div>
            </div>
            
            @if(!$r->ended_at)
                <span class="badge bg-warning text-dark">Đang sửa</span>
            @else
                <span class="text-muted small">Done {{ \Carbon\Carbon::parse($r->ended_at)->format('H:i d/m') }}</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $repairs->onEachSide(1)->links() }}
</div>
@endsection
