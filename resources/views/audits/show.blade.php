@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Chi tiết đánh giá')

@section('content')
<div class="mb-4">
    <a href="/audits" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Quay lại
    </a>
    <div class="d-flex align-items-center gap-3 mb-2">
        <h3 class="fw-bold mb-0">Chi tiết phiếu đánh giá #{{ $audit->id }}</h3>
        <span class="badge bg-success bg-opacity-10 text-success border border-success">Đã hoàn thành</span>
    </div>
    <div class="text-muted mb-4">
        <span>🕒 Thời gian: <strong>{{ $audit->created_at->format('H:i d/m/Y') }}</strong></span>
        <span class="mx-2">•</span>
        <span>👤 Người thực hiện: <strong>{{ $audit->auditor->name ?? 'N/A' }}</strong></span>
    </div>

    <!-- Thông tin tổ/bộ phận -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">Mẫu đánh giá</div>
                    <div class="h4 fw-bold mb-1">{{ $audit->template->name }}</div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">Bộ phận</div>
                    <div class="h5 fw-bold mb-0 text-info">{{ $audit->template->department_name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách kết quả -->
    <h4 class="h5 mb-3 fw-bold text-dark mt-5">Kết quả kiểm tra thực tế</h4>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-0">
            @forelse($audit->results as $index => $result)
                <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }} bg-white">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Icon status -->
                        <div class="mt-1 flex-shrink-0">
                            @if($result->is_passed)
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            @else
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Tiêu đề & Ghi chú -->
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark lh-base fs-6 mb-1">
                                {{ $result->criterion->content ?? 'Câu hỏi đã bị xóa' }}
                            </h5>
                            
                            @if(!$result->is_passed)
                                <div class="mt-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded p-3 text-danger">
                                    <div class="fw-bold d-flex align-items-center gap-1 mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        Nội dung Lỗi phát hiện:
                                    </div>
                                    <div style="white-space: pre-wrap;" class="mb-2">{{ $result->note }}</div>

                                    @if($result->image_path)
                                        <div class="mt-3">
                                            <div class="fw-bold d-flex align-items-center gap-1 mb-2 text-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                Ảnh đính kèm:
                                            </div>
                                            <a href="/{{ $result->image_path }}" target="_blank">
                                                <img src="/{{ $result->image_path }}" class="img-fluid rounded border shadow-sm" style="max-height: 200px; object-fit: contain;" alt="Lỗi đính kèm">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Status text badge -->
                        <div class="flex-shrink-0 ms-3 d-none d-md-block">
                            @if($result->is_passed)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fs-6">ĐẠT (V)</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 fs-6">LỖI (X)</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-5 text-center text-muted">
                    Không tìm thấy kết quả chi tiết cho phiên đánh giá này.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
