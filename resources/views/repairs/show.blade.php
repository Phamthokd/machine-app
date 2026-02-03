@extends('layouts.app-simple')
@section('title', 'Chi tiết phiếu sửa ' . $repair->code)

@section('content')
<style>
    .card-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: none;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-header {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 600;
        color: #334155;
        background: #f8fafc;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-label {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 4px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.025em;
    }
    .info-value {
        color: #0f172a;
        font-weight: 500;
        font-size: 1rem;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
    }
    .status-badge.submitted { background-color: #e0f2fe; color: #0284c7; }
    .status-badge.completed { background-color: #dcfce7; color: #16a34a; }
</style>

<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="/repairs" class="text-decoration-none text-secondary d-flex align-items-center gap-1 small fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    QUAY LẠI DANH SÁCH
                </a>
            </div>
            <h3 class="fw-bold mb-0">Phiếu: {{ $repair->code }}</h3>
        </div>
        <div>
            <span class="status-badge {{ $repair->status }}">
                {{ $repair->status == 'completed' ? 'Đã hoàn thành' : 'Đã ghi nhận' }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cot trai: Thong tin may & Noi dung -->
        <div class="col-12 col-lg-8">
            <!-- Machine Info -->
            <div class="card-section">
                <div class="section-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Thông tin thiết bị
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label">Mã thiết bị</div>
                            <div class="info-value text-primary fw-bold">
                                <a href="/m/{{ $repair->machine->ma_thiet_bi }}" class="text-decoration-none">{{ $repair->machine->ma_thiet_bi }}</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Tổ / Chuyền</div>
                            <div class="info-value">{{ $repair->machine->department->name ?? '---' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Tên thiết bị</div>
                            <div class="info-value">{{ $repair->machine->ten_thiet_bi }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repair Details -->
            <div class="card-section">
                <div class="section-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Nội dung sửa chữa
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label">Mã hàng</div>
                            <div class="info-value">{{ $repair->ma_hang }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Công đoạn</div>
                            <div class="info-value">{{ $repair->cong_doan }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label text-danger">Nguyên nhân hư hỏng</div>
                            <div class="p-3 bg-light rounded-3 mt-1">
                                {{ $repair->nguyen_nhan }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label text-success">Biện pháp khắc phục</div>
                            <div class="p-3 bg-light rounded-3 mt-1">
                                {{ $repair->noi_dung_sua_chua }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cot phai: Thoi gian & Nhan su -->
        <div class="col-12 col-lg-4">
            <div class="card-section h-100">
                <div class="section-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Thời gian & Nhân sự
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <div class="info-label">Người tạo phiếu</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 14px;">
                                {{ substr($repair->createdBy->name ?? '?', 0, 1) }}
                            </div>
                            <div class="fw-medium">{{ $repair->createdBy->name ?? 'Không xác định' }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="info-label">Bắt đầu sửa</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($repair->started_at)->format('H:i d/m/Y') }}</div>
                        </div>
                        @if($repair->ended_at)
                        <div class="col-12">
                            <div class="info-label">Hoàn thành</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($repair->ended_at)->format('H:i d/m/Y') }}</div>
                        </div>
                        @endif
                    </div>

                    <hr class="opacity-10 my-4">

                    <h6 class="fw-bold text-secondary text-uppercase text-xs mb-3">Xác nhận QC / QA</h6>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Endline QC</span>
                            <span class="fw-bold">{{ $repair->endline_qc_name }}</span>
                        </div>
                        @if($repair->inline_qc_name)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Inline QC</span>
                            <span class="fw-bold">{{ $repair->inline_qc_name }}</span>
                        </div>
                        @endif
                        @if($repair->qa_supervisor_name)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Chủ quản QA</span>
                            <span class="fw-bold">{{ $repair->qa_supervisor_name }}</span>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
