@php
    $failedResults = $audit->results->where('is_passed', 0);
    $canImprove = auth()->user()->managed_department === 'Bán thành phẩm' 
        && $audit->template->name === 'Đánh giá bộ phận BTP'
        && $failedResults->isNotEmpty();
        
    // Kiểm tra xe người dùng hiện tại có quyền đánh giá lại hay không
    $canReview = \Illuminate\Support\Facades\Auth::check() 
        && (auth()->user()->hasRole('audit') || auth()->user()->hasRole('admin'))
        && empty(auth()->user()->managed_department);
    
    // Lấy danh sách các câu hỏi đã được báo cải thiện xong nhưng chưa được review
    // (Và điều kiện bổ sung: phải đến hoặc qua ngày deadline thì mới được Đánh giá lại)
    $reviewableResults = $audit->results->filter(function($r) {
        if (empty($r->improver_name) || !empty($r->reviewer_name)) {
            return false;
        }
        
        // Nếu không có deadline (lý do nào đó), mặc định cho phép đánh giá luôn
        if (empty($r->improvement_deadline)) {
            return true;
        }

        // So sánh ngày hiện tại với ngày deadline (bỏ qua giờ phút)
        $today = \Carbon\Carbon::now()->startOfDay();
        $deadline = \Carbon\Carbon::parse($r->improvement_deadline)->startOfDay();
        
        return $today->gte($deadline);
    });
@endphp

@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.audit_detail'))

@section('content')
<div class="mb-4">
    <a href="/audits" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        {{ __('messages.back') }}
    </a>
    
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex align-items-center flex-wrap gap-3 mb-2">
        <div class="d-flex align-items-center gap-3">
            <h3 class="fw-bold mb-0">{{ __('messages.audit_detail_id') }} #{{ $audit->id }}</h3>
            <span class="badge bg-success bg-opacity-10 text-success border border-success">{{ __('messages.audit_completed') }}</span>
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            @if($canImprove)
                <button type="button" class="btn btn-sm btn-warning d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#improvementModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    Cải thiện
                </button>
            @endif
            <a href="{{ route('audits.export_detail', $audit->id) }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Xuất Excel
            </a>
        </div>
    </div>
    <div class="text-muted mb-4">
        <span>🕒 {{ __('messages.time') }}: <strong>{{ $audit->created_at->format('H:i d/m/Y') }}</strong></span>
        <span class="mx-2">•</span>
        <span>👤 {{ __('messages.auditor_name') }} <strong>{{ $audit->auditor->name ?? 'N/A' }}</strong></span>
    </div>

    <!-- Thông tin tổ/bộ phận -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">{{ __('messages.audit_template_label') }}</div>
                    <div class="h4 fw-bold mb-1">{{ __($audit->template->name) }}</div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">{{ __('messages.department') }}</div>
                    <div class="h5 fw-bold mb-2 text-info">{{ __($audit->template->department_name) }}</div>
                    <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 px-3 py-2 rounded-3 border border-white border-opacity-10">
                        <span class="text-white-50 text-uppercase fw-bold text-xs">Điểm số:</span>
                        <span class="fs-5 fw-bold {{ $audit->score == 100 ? 'text-success' : ($audit->score >= 80 ? 'text-warning' : 'text-danger') }}">{{ $audit->score }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách kết quả -->
    <h4 class="h5 mb-3 fw-bold text-dark mt-5">{{ __('messages.actual_inspection_result') }}</h4>
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
                                {{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}
                            </h5>
                            
                            @if(!$result->is_passed)
                                <div class="mt-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded p-3 text-danger mb-3">
                                    <div class="fw-bold d-flex align-items-center gap-1 mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        {{ __('messages.detected_error_content') }}
                                    </div>
                                    <div style="white-space: pre-wrap;" class="mb-2">{{ $result->note }}</div>

                                    @if($result->image_path)
                                        <div class="mt-3">
                                            <div class="fw-bold d-flex align-items-center gap-1 mb-2 text-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                {{ __('messages.attached_image') }}
                                            </div>
                                            <a href="/{{ $result->image_path }}" target="_blank">
                                                <img src="/{{ $result->image_path }}" class="img-fluid rounded border shadow-sm" style="max-height: 200px; object-fit: contain;" alt="Lỗi đính kèm">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($result->root_cause)
                                    <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded p-3 text-dark mb-3">
                                        <h6 class="fw-bold text-warning mb-3 d-flex align-items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                            Kế hoạch cải thiện
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="text-muted small fw-bold mb-1">Nguyên nhân gốc rễ</div>
                                                <div style="white-space: pre-wrap;">{{ $result->root_cause }}</div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="text-muted small fw-bold mb-1">Kế hoạch khắc phục</div>
                                                <div style="white-space: pre-wrap;">{{ $result->corrective_action }}</div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="text-muted small fw-bold mb-1">Thời gian cải thiện xong</div>
                                                <div>{{ \Carbon\Carbon::parse($result->improvement_deadline)->format('d/m/Y') }}</div>
                                            </div>
                                            @if($result->improver_name)
                                                <div class="col-md-12">
                                                    <div class="text-muted small fw-bold mb-1">Người cải thiện</div>
                                                    <div class="fw-bold text-dark">{{ $result->improver_name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($result->reviewer_name)
                                        <div class="bg-info bg-opacity-10 border border-info border-opacity-25 rounded p-3 text-dark">
                                            <h6 class="fw-bold text-info mb-3 d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                                                Kết quả Đánh giá lại
                                            </h6>
                                            <div class="row g-3">
                                                @if($result->review_note)
                                                    <div class="col-md-12">
                                                        <div class="text-muted small fw-bold mb-1">Nhận xét của Audit</div>
                                                        <div style="white-space: pre-wrap;">{{ $result->review_note }}</div>
                                                    </div>
                                                @endif
                                                @if($result->review_image_path)
                                                    @php
                                                        // Fallback cho cả record cũ (lưu là public/audits/...) và mới (lưu là storage/audits/...)
                                                        $r_img = str_starts_with($result->review_image_path, 'public/')
                                                            ? '/' . str_replace('public/', 'storage/', $result->review_image_path)
                                                            : '/' . ltrim($result->review_image_path, '/');
                                                    @endphp
                                                    <div class="col-md-12 mt-3">
                                                        <div class="text-muted small fw-bold mb-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                                                            Hình ảnh sau cải thiện
                                                        </div>
                                                        <a href="{{ $r_img }}" target="_blank" class="d-inline-block position-relative rounded overflow-hidden shadow-sm" style="border: 2px solid #e2e8f0; width: 120px; height: 120px;">
                                                            <img src="{{ $r_img }}" alt="Review Image" class="w-100 h-100" style="object-fit: cover; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="col-md-12 mt-3">
                                                    <div class="d-flex align-items-center gap-3 text-muted small">
                                                        <span>👤 Audit ghi nhận: <strong class="text-dark">{{ $result->reviewer_name }}</strong></span>
                                                        <span>🕒 Thời gian: <strong>{{ \Carbon\Carbon::parse($result->reviewed_at)->format('H:i d/m/Y') }}</strong></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                        
                        <!-- Status text badge -->
                        <div class="flex-shrink-0 ms-3 d-none d-md-block">
                            @if($result->is_passed)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fs-6">{{ __('messages.audit_pass') }}</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 fs-6">{{ __('messages.audit_fail') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-5 text-center text-muted">
                    {{ __('messages.no_detailed_result') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

@if($canImprove)
<!-- Modal Cải thiện -->
<div class="modal fade" id="improvementModal" tabindex="-1" aria-labelledby="improvementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.improvements', $audit->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-warning bg-opacity-10 border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="improvementModalLabel">
                    <svg class="text-warning" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    Kế hoạch Cải thiện Lỗi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted mb-4">Vui lòng điền đầy đủ kế hoạch cải thiện cho các hạng mục bị đánh giá lỗi dưới đây.</p>
                
                @foreach($failedResults as $index => $result)
                    <div class="card bg-light border-0 shadow-sm mb-4 rounded-3">
                        <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 py-3">
                            <div class="d-flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-1"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <div>
                                    <div class="fs-6">{{ $result->criterion ? $result->criterion->content : 'Hạng mục đã xóa' }}</div>
                                    <div class="fw-normal small mt-1">Lỗi: {{ $result->note }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nguyên nhân gốc rễ <span class="text-danger">*</span></label>
                                <textarea class="form-control bg-white" name="improvements[{{ $result->id }}][root_cause]" rows="2" required placeholder="Nhập nguyên nhân gây ra lỗi...">{{ old("improvements.{$result->id}.root_cause", $result->root_cause) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kế hoạch khắc phục <span class="text-danger">*</span></label>
                                <textarea class="form-control bg-white" name="improvements[{{ $result->id }}][corrective_action]" rows="2" required placeholder="Hành động để khắc phục lỗi triệt để...">{{ old("improvements.{$result->id}.corrective_action", $result->corrective_action) }}</textarea>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Thời gian cải thiện xong <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-white" name="improvements[{{ $result->id }}][improvement_deadline]" value="{{ old("improvements.{$result->id}.improvement_deadline", $result->improvement_deadline ? \Carbon\Carbon::parse($result->improvement_deadline)->format('Y-m-d') : '') }}" required>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm">LƯU CẢI THIỆN</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($canReview && $reviewableResults->isNotEmpty())
<!-- Nút nổi để mở modal Đánh giá lại -->
<div class="position-fixed bottom-0 start-50 translate-middle-x w-100 p-3" style="max-width: 800px; z-index: 1040;">
    <button type="button" class="btn btn-info w-100 shadow-lg text-white" style="border-radius: 12px; padding: 14px 20px; font-weight: 600; font-size: 16px;" data-bs-toggle="modal" data-bs-target="#reviewModal">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
            Đánh giá lại cải thiện ({{ $reviewableResults->count() }})
        </div>
    </button>
</div>

<!-- Modal Đánh giá lại -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.reviews', $audit->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-info bg-opacity-10 border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="reviewModalLabel">
                    <svg class="text-info" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    Đánh giá lại kết quả Cải thiện
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted mb-4">Vui lòng chụp ảnh và nhận xét các hạng mục đã được báo cáo cải thiện dưới đây.</p>
                
                @foreach($reviewableResults as $index => $result)
                    <div class="card bg-light border-0 shadow-sm mb-4 rounded-3 text-dark">
                        <div class="card-header bg-info bg-opacity-10 text-dark fw-bold border-0 py-3">
                            <div class="d-flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-info flex-shrink-0 mt-1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="3"/><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/></svg>
                                <div>
                                    <div class="fs-6">{{ $result->criterion ? $result->criterion->content : 'Hạng mục đã xóa' }}</div>
                                    <div class="fw-normal small mt-1 text-muted">Báo cải thiện bởi: {{ $result->improver_name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="reviews[{{ $index }}][result_id]" value="{{ $result->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">Ảnh chụp sau cải thiện</label>
                                <input type="file" name="reviews[{{ $index }}][review_image]" class="form-control" accept="image/*" capture="environment">
                                <div class="form-text">📝 Dùng điện thoại chụp ảnh thực tế tình trạng hiện tại.</div>
                            </div>

                            <div>
                                <label class="form-label fw-bold small text-secondary">Nhận xét của Audit</label>
                                <textarea name="reviews[{{ $index }}][review_note]" class="form-control" rows="2" placeholder="Ghi nhận xét về cải thiện này..."></textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer bg-light border-top-0 pt-0 pb-3">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-info text-white fw-bold px-4 shadow-sm">Lưu Đánh Giá</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
