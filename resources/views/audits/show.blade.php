@php
$failedResults = $audit->results->where('is_passed', 0);
$isFullyReviewed = $audit->results->contains(function($r) { return !empty($r->improver_name); }) &&
$audit->results->filter(function($r) { return !empty($r->improver_name) && empty($r->reviewer_name); })->isEmpty();

// Quyền truy cập
$userDept = auth()->user()->managed_department;
$auditDept = $audit->template->department_name ?? null;
$isAdmin = auth()->user()->hasRole('admin');

$userDeptMapped = $userDept === 'Bán thành phẩm' ? 'BTP' : $userDept;

$isDepartmentUser = \Illuminate\Support\Facades\Auth::check() && (
    !$isAdmin && (!empty($userDeptMapped) && !empty($auditDept) && $userDeptMapped === $auditDept)
);

$isAuditUser = \Illuminate\Support\Facades\Auth::check()
&& (auth()->user()->hasRole('audit') || auth()->user()->hasRole('admin'))
&& empty(auth()->user()->managed_department);

// Phân loại lỗi
$unrespondedResults = $failedResults->filter(function($r) {
    return is_null($r->department_agreement);
});

$rejectedResultsPendingAudit = $failedResults->filter(function($r) {
    return $r->department_agreement === false && is_null($r->audit_rejection_decision);
});

$improveableResults = $failedResults->filter(function($r) {
    return $r->department_agreement === true || $r->audit_rejection_decision === false;
});

// Điều kiện hiển thị các nút
$canRespond = $isDepartmentUser && $unrespondedResults->isNotEmpty();
$canReviewRejections = $isAuditUser && $rejectedResultsPendingAudit->isNotEmpty();

$canImprove = $isDepartmentUser
&& $improveableResults->isNotEmpty()
&& (!$isFullyReviewed || (auth()->check() && auth()->user()->hasRole('admin')));

$canReview = $isAuditUser && (!$isFullyReviewed || (auth()->check() && auth()->user()->hasRole('admin')));

// Lấy danh sách cải thiện cần Audit duyệt KQ (Chỉ hiện khi bộ phận đã báo cáo hoàn thiện)
$reviewableResults = $audit->results->filter(function($r) {
    return !empty($r->improver_name) && empty($r->reviewer_name) && $r->is_completed;
});

// Lấy danh sách cần bộ phận báo cáo hoàn thành
$completableResults = $improveableResults->filter(function($r) {
    return !empty($r->root_cause) && !$r->is_completed;
});
@endphp

@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.audit_detail'))

@section('content')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
        --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        margin-bottom: 1.5rem;
    }

    .btn-back:hover {
        color: #3b82f6;
        transform: translateX(-4px);
    }

    .detail-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .info-bar {
        background: #1e293b;
        color: white;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .result-card {
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
        transition: all 0.2s;
        background: white;
    }

    .result-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }

    .photo-thumb {
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #eef2f6;
    }

    .photo-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="mb-5">
    <a href="/audits" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6" />
        </svg>
        {{ __('messages.back') }}
    </a>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 pulse">
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="d-flex align-items-center flex-wrap justify-content-between gap-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="card-icon" style="width: 56px; height: 56px; background: #eff6ff; color: #2563eb; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">#</div>
            <div>
                <h2 class="h3 fw-bold mb-1">{{ __('messages.audit_detail') }} #{{ $audit->id }}</h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-none" style="padding: 2px 10px;">{{ __('messages.audit_completed') }}</span>
                    <span class="text-muted small">🕒 {{ $audit->created_at->format('H:i d/m/Y') }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($canRespond)
            <button type="button" class="btn btn-info shadow-sm fw-bold px-4 text-white rounded-3" data-bs-toggle="modal" data-bs-target="#respondModal">
                {{ __('messages.audit_respond_btn') }}
            </button>
            @endif
            @if($canReviewRejections)
            <button type="button" class="btn btn-danger shadow-sm fw-bold px-4 text-white rounded-3" data-bs-toggle="modal" data-bs-target="#reviewRejectionsModal">
                {{ __('messages.audit_review_dispute_modal_title') }}
            </button>
            @endif
            @if($canImprove)
            <button type="button" class="btn btn-warning shadow-sm fw-bold px-4 rounded-3" data-bs-toggle="modal" data-bs-target="#improvementModal">
                {{ __('messages.improvement_plan') }}
            </button>
            @endif
            @if($isDepartmentUser && $completableResults->isNotEmpty())
            <button type="button" class="btn btn-success shadow-sm fw-bold px-4 rounded-3" data-bs-toggle="modal" data-bs-target="#confirmCompletionModal">
                {{ __('messages.audit_confirm_completion_btn') }}
            </button>
            @endif
            @if((auth()->user()->hasRole('admin') || (auth()->user()->hasRole('audit') && empty(auth()->user()->managed_department))) && (!$isFullyReviewed || auth()->user()->hasRole('admin')))
            <a href="{{ route('audits.edit', $audit->id) }}" class="btn btn-outline-warning fw-bold px-4 rounded-3">
                {{ __('messages.edit_audit') }}
            </a>
            @endif
            <a href="{{ route('audits.export_detail', $audit->id) }}" class="btn btn-light border fw-bold px-4 rounded-3 text-success d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="7 10 12 15 17 10" /><line x1="12" y1="15" x2="12" y2="3" /></svg>
                {{ __('messages.export_excel') }}
            </a>
        </div>
    </div>

    <!-- Thông tin tổ/bộ phận -->
    <div class="info-bar">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <div class="text-white-50 text-uppercase fw-bold small mb-2" style="letter-spacing: 0.1em">{{ __('messages.audit_template_label') }}</div>
                <h3 class="h2 fw-bold mb-0">{{ __($audit->template->name) }}</h3>
                <div class="mt-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2 bg-white bg-opacity-10 px-3 py-1 rounded-pill">
                        <span class="text-white-50 small">👤</span>
                        <span class="small fw-medium">{{ $audit->auditor->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-block text-start text-md-end">
                    <div class="text-white-50 text-uppercase fw-bold small mb-2" style="letter-spacing: 0.1em">{{ __('messages.department') }}</div>
                    <h4 class="h5 fw-bold text-info mb-4">{{ __('messages.' . $audit->template->department_name) }}</h4>
                    <div class="d-inline-flex flex-column align-items-md-end gap-1">
                        <div class="text-white-50 text-uppercase fw-bold small" style="letter-spacing: 0.1em">{{ __('messages.pass_rate') }}:</div>
                        <div class="h1 fw-black display-5 mb-0 {{ $audit->score == 100 ? 'text-success' : ($audit->score >= 80 ? 'text-warning' : 'text-danger') }}">{{ $audit->score }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách kết quả -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-5">
        <h4 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <span style="width: 4px; height: 24px; background: #2563eb; border-radius: 4px; display: inline-block;"></span>
            {{ __('messages.actual_inspection_result') }}
        </h4>
        <div class="text-muted small fw-medium">
            {{ __('messages.total') }}: <strong>{{ $audit->results->count() }}</strong> {{ __('messages.items') }}
        </div>
    </div>

    <div class="results-container d-grid gap-4">
        @forelse($audit->results as $index => $result)
        <div class="result-card p-0 overflow-hidden shadow-sm">
            <div class="p-4">
                <div class="d-flex align-items-start gap-4">
                    <!-- Icon status -->
                    <div class="flex-shrink-0">
                        @if($result->is_passed)
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             style="width: 48px; height: 48px; background: #f0fdf4; color: #16a34a; border: 2px solid #bcf0da;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             style="width: 48px; height: 48px; background: #fef2f2; color: #dc2626; border: 2px solid #fecaca;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- Nội dung chính -->
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                            <h5 class="fw-bold text-dark lh-base fs-6 mb-0" style="max-width: 80%; text-wrap: balance;">
                                {{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}
                            </h5>
                            <div class="text-end">
                                @if($result->is_passed)
                                <span class="status-badge bg-success bg-opacity-10 text-success border-success border-opacity-25">{{ __('messages.audit_pass') }}</span>
                                @if($result->audit_rejection_decision === true)
                                <div class="mt-1 small text-info fw-bold">{{ __('messages.audit_error_cancelled') }}</div>
                                @endif
                                @else
                                <span class="status-badge bg-danger bg-opacity-10 text-danger border-danger border-opacity-25">{{ __('messages.audit_fail') }}</span>
                                @endif
                            </div>
                        </div>

                        @if(!$result->is_passed)
                        <!-- Chi tiết lỗi -->
                        <div class="mt-4 p-4 rounded-4 bg-light border border-dashed border-danger border-opacity-25">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-grow-1">
                                    <div class="text-danger fw-bold small text-uppercase mb-2 d-flex align-items-center gap-2" style="letter-spacing: 0.05em">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ __('messages.detected_error_content') }}
                                    </div>
                                    <div class="text-dark fs-6" style="white-space: pre-wrap;">{{ $result->note }}</div>

                                    @if(!empty($result->image_path))
                                    <div class="mt-4">
                                        <div class="small fw-bold text-secondary mb-2">📸 {{ __('messages.attached_image') }}:</div>
                                        <div class="photo-grid">
                                            @foreach((array)$result->image_path as $path)
                                            <div class="photo-thumb shadow-sm">
                                                <a href="/{{ $path }}" target="_blank">
                                                    <img src="/{{ $path }}" alt="Lỗi">
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Trạng thái phản hồi -->
                        @if($result->department_agreement === false)
                        <div class="mt-4 p-4 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                            <div class="d-flex align-items-center gap-2 text-warning fw-bold small text-uppercase mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                {{ __('messages.audit_dispute_reason_label') }}
                            </div>
                            <div class="bg-white p-3 rounded-3 shadow-sm text-dark fs-6 mb-3" style="white-space: pre-wrap;">{{ $result->department_reject_reason }}</div>

                            @if(is_null($result->audit_rejection_decision))
                            <div class="d-inline-flex align-items-center gap-2 text-secondary small fw-medium">
                                <span class="pulse-dot"></span> {{ __('messages.audit_waiting_dispute_approval') }}
                            </div>
                            @elseif($result->audit_rejection_decision === false)
                            <div class="text-danger small fw-bold d-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                {{ __('messages.audit_dispute_rejected') }}
                            </div>
                            @endif
                        </div>
                        @elseif($result->department_agreement === true)
                        <div class="mt-3 text-success small fw-bold d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            {{ __('messages.audit_error_confirmed') }}
                        </div>
                        @endif

                        <!-- Kế hoạch cải thiện -->
                        @if($result->root_cause)
                        <div class="mt-4 p-4 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <div class="text-info fw-bold small text-uppercase mb-3 d-flex align-items-center gap-2" style="letter-spacing: 0.05em">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                {{ __('messages.improvement_plan') }}
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="text-muted small fw-bold mb-1">{{ __('messages.root_cause') }}</div>
                                    <div class="bg-white p-3 rounded-3 shadow-sm text-dark">{{ $result->root_cause }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small fw-bold mb-1">{{ __('messages.corrective_action') }}</div>
                                    <div class="bg-white p-3 rounded-3 shadow-sm text-dark">{{ $result->corrective_action }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small fw-bold mb-1">{{ __('messages.improvement_deadline') }}</div>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($result->improvement_deadline)->format('d/m/Y') }}</div>
                                </div>
                                @if($result->improver_name)
                                <div class="col-md-4">
                                    <div class="text-muted small fw-bold mb-1">{{ __('messages.improver') }}</div>
                                    <div class="fw-bold text-dark">{{ $result->improver_name }}</div>
                                </div>
                                @endif
                                <div class="col-md-4 text-md-end pt-3">
                                    @if($result->reviewer_name)
                                    <span class="badge bg-success px-3 py-2 rounded-pill fw-bold">✓ {{ __('messages.audit_status_completed') }}</span>
                                    @elseif($result->is_completed)
                                    <div class="d-flex flex-column align-items-md-end gap-2">
                                        <span class="badge bg-info text-white px-3 py-2 rounded-pill fw-bold">⌛ {{ __('messages.audit_status_pending_review') }}</span>
                                        @if($isAdmin)
                                        <form action="{{ route('audits.reject_completion', [$audit->id, $result->id]) }}" method="POST" data-confirm-msg="{{ __('messages.audit_confirm_reject_completion') }}" onsubmit="return confirm(this.dataset.confirmMsg)">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-2 py-1 rounded-3" style="font-size: 10px;">
                                                <i class="fas fa-undo me-1"></i> {{ __('messages.audit_request_update') }}
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    @else
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">⌛ {{ __('messages.audit_status_processing') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($result->is_completed)
                            <div class="mt-4 border-top pt-4">
                                <div class="text-success fw-bold small text-uppercase mb-3 d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    {{ __('messages.audit_view_improvement_report') }}
                                </div>
                                <div class="bg-white p-4 rounded-4 shadow-sm">
                                    @if($result->completion_image_path)
                                    <div class="mb-3">
                                        <div class="text-muted small fw-bold mb-2">📸 {{ __('messages.audit_completion_image_label') }}:</div>
                                        <div class="photo-grid">
                                            @foreach((array)$result->completion_image_path as $p_path)
                                            <div class="photo-thumb shadow-sm">
                                                <a href="/{{ $p_path }}" target="_blank">
                                                    <img src="/{{ $p_path }}" alt="Completion Proof">
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    @if($result->completion_note)
                                    <div class="mb-3">
                                        <div class="text-muted small fw-bold mb-1">📝 {{ __('messages.audit_completion_note_label') }}:</div>
                                        <div class="bg-light p-3 rounded-3 text-dark fs-6" style="white-space: pre-wrap;">{{ $result->completion_note }}</div>
                                    </div>
                                    @endif
                                    <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center gap-4 text-muted small">
                                        <span>📅 {{ __('messages.report_time') }}: <strong class="text-dark">{{ \Carbon\Carbon::parse($result->completed_at)->format('H:i d/m/Y') }}</strong></span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($result->reviewer_name)
                            <div class="mt-4 border-top pt-4">
                                <div class="text-success fw-bold small text-uppercase mb-3 d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                                    {{ __('messages.re_evaluation_result') }}
                                </div>
                                <div class="bg-white p-4 rounded-4 shadow-sm">
                                    @if($result->review_note)
                                    <div class="mb-3">
                                        <div class="text-muted small fw-bold mb-1">{{ __('messages.audit_note') }}</div>
                                        <div class="fs-6">{{ $result->review_note }}</div>
                                    </div>
                                    @endif
                                    @if($result->review_image_path)
                                    @php
                                        $r_img = str_starts_with($result->review_image_path, 'public/')
                                        ? '/storage/' . str_replace('public/', '', $result->review_image_path)
                                        : '/' . ltrim($result->review_image_path, '/');
                                    @endphp
                                    <div class="mb-3">
                                        <div class="text-muted small fw-bold mb-2">📸 {{ __('messages.audit_improvement_image_label') }}:</div>
                                        <div class="photo-thumb" style="width: 150px; height: 150px">
                                            <a href="{{ $r_img }}" target="_blank">
                                                <img src="{{ $r_img }}" alt="Review Image">
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center gap-4 text-muted small">
                                        <span>👤 {{ __('messages.reviewer') }}: <strong class="text-dark">{{ $result->reviewer_name }}</strong></span>
                                        <span>📅 {{ __('messages.time') }}: <strong class="text-dark">{{ \Carbon\Carbon::parse($result->reviewed_at)->format('H:i d/m/Y') }}</strong></span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <div class="mb-3 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
            </div>
            <h5 class="text-muted fw-bold">{{ __('messages.no_detailed_result') }}</h5>
        </div>
        @endforelse
    </div>
</div>

@if($canRespond)
<!-- Modal Phản hồi lỗi -->
<div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.agreements', $audit->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-3" id="respondModalLabel">
                    <div class="rounded-3 bg-info bg-opacity-10 p-2 text-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    {{ __('messages.audit_feedback_modal_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4 fs-6">{{ __('messages.audit_feedback_modal_desc') }}</p>

                @foreach($unrespondedResults as $index => $result)
                <div class="card result-card shadow-none border mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 p-3">
                        <div class="d-flex gap-3">
                            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 28px; height: 28px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fs-6 lh-sm">{{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}</div>
                                <div class="fw-normal small mt-1 text-danger-emphasis opacity-75">{{ __('messages.detected_error_content') }} {{ $result->note }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.audit_decision_label') }} <span class="text-danger">*</span></label>
                            <select class="form-select border-0 bg-light rounded-3 py-2 px-3 fw-medium" name="agreements[{{ $result->id }}][department_agreement]" required
                                onchange="document.getElementById('reject_reason_{{ $result->id }}').style.display = this.value === '0' ? 'block' : 'none';">
                                <option value="" disabled selected>{{ __('messages.audit_choose_feedback') }}</option>
                                <option value="1">✅ {{ __('messages.audit_agree_error_option') }}</option>
                                <option value="0">❌ {{ __('messages.audit_dispute_error_option') }}</option>
                            </select>
                        </div>
                        <div class="mb-0 animate__animated animate__fadeIn" id="reject_reason_{{ $result->id }}" style="display: none;">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.audit_dispute_reason_input_label') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control border-0 bg-light rounded-3 py-2 px-3" name="agreements[{{ $result->id }}][department_reject_reason]" rows="3" placeholder="{{ __('messages.audit_dispute_reason_placeholder') }}"></textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-3 h-48" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-info fw-bold px-4 rounded-3 h-48 text-white shadow-sm flex-grow-1">{{ __('messages.audit_send_feedback_btn') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($canReviewRejections)
<!-- Modal Duyệt phản đối -->
<div class="modal fade" id="reviewRejectionsModal" tabindex="-1" aria-labelledby="reviewRejectionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.review_rejections', $audit->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-3" id="reviewRejectionsModalLabel">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-2 text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                    </div>
                    {{ __('messages.audit_review_dispute_modal_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4 fs-6">{{ __('messages.audit_review_dispute_modal_desc') }}</p>

                @foreach($rejectedResultsPendingAudit as $index => $result)
                <div class="card result-card shadow-none border mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 p-3">
                        <div class="d-flex gap-3">
                            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 28px; height: 28px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fs-6 lh-sm">{{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}</div>
                                <div class="fw-normal small mt-1 text-danger-emphasis opacity-75">{{ __('messages.initial_error') }}: {{ $result->note }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="p-3 bg-light rounded-3 mb-4">
                            <span class="fw-bold text-secondary small text-uppercase d-block mb-2" style="letter-spacing: 0.05em">{{ __('messages.audit_dispute_reason_label') }}</span>
                            <div class="text-dark bg-white p-3 rounded-2 shadow-sm fs-6">{{ $result->department_reject_reason }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.audit_decision_label') }} <span class="text-danger">*</span></label>
                            <select class="form-select border-0 bg-light rounded-3 py-2 px-3 fw-medium" name="rejections[{{ $result->id }}][decision]" required>
                                <option value="" disabled selected>{{ __('messages.audit_choose_decision') }}</option>
                                <option value="1">✅ {{ __('messages.audit_accept_dispute_option') }}</option>
                                <option value="0">❌ {{ __('messages.audit_reject_dispute_option') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-3 h-48" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-danger fw-bold px-4 rounded-3 h-48 shadow-sm flex-grow-1">{{ __('messages.save_final_decision') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($canImprove)
<!-- Modal Cải thiện -->
<div class="modal fade" id="improvementModal" tabindex="-1" aria-labelledby="improvementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.improvements', $audit->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-3" id="improvementModalLabel">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-2 text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </div>
                    {{ __('messages.improvement_plan') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4 fs-6">{{ __('messages.audit_improvement_form_desc') }}</p>

                @foreach($improveableResults as $index => $result)
                <div class="card result-card shadow-none border mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 p-3">
                        <div class="d-flex gap-3">
                            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 28px; height: 28px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fs-6 lh-sm">{{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}</div>
                                <div class="fw-normal small mt-1 text-danger-emphasis opacity-75">{{ __('messages.issue') }}: {{ $result->note }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.root_cause') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control border-0 bg-light rounded-3 py-2 px-3" name="improvements[{{ $result->id }}][root_cause]" rows="2" required placeholder="{{ __('messages.root_cause_placeholder') }}" {{ !empty($result->improver_name) && !auth()->user()->hasRole('admin') ? 'readonly' : '' }}>{{ old("improvements.{$result->id}.root_cause", $result->root_cause) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.corrective_action') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control border-0 bg-light rounded-3 py-2 px-3" name="improvements[{{ $result->id }}][corrective_action]" rows="2" required placeholder="{{ __('messages.corrective_action_placeholder') }}" {{ !empty($result->improver_name) && !auth()->user()->hasRole('admin') ? 'readonly' : '' }}>{{ old("improvements.{$result->id}.corrective_action", $result->corrective_action) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.improvement_deadline') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control border-0 bg-light rounded-3 py-2 px-3 fw-medium" name="improvements[{{ $result->id }}][improvement_deadline]" value="{{ old("improvements.{$result->id}.improvement_deadline", $result->improvement_deadline ? \Carbon\Carbon::parse($result->improvement_deadline)->format('Y-m-d') : '') }}" required {{ !empty($result->improver_name) && !auth()->user()->hasRole('admin') ? 'readonly' : '' }} onkeydown="return false">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-3 h-48" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                @php
                    $pendingImprovements = $improveableResults->filter(fn($r) => empty($r->improver_name))->count();
                @endphp
                @if($isAdmin || $pendingImprovements > 0)
                <button type="submit" class="btn btn-warning fw-bold px-4 rounded-3 h-48 shadow-sm flex-grow-1">{{ __('messages.save_improvement_plan') }}</button>
                @else
                <div class="alert alert-info py-2 px-3 rounded-3 small mb-0 flex-grow-1 text-center">
                    <i class="fas fa-lock me-1"></i> {{ __('messages.improvement_plan_locked') }}
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endif

@if($canReview && $reviewableResults->isNotEmpty())
<!-- Nút nổi để mở modal Đánh giá lại -->
<div class="position-fixed bottom-0 start-50 translate-middle-x w-100 p-3" style="max-width: 800px; z-index: 1040;">
    <button type="button" class="btn btn-info w-100 shadow-lg text-white" style="border-radius: 16px; padding: 14px 20px; font-weight: 700; font-size: 16px; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); border: none;" data-bs-toggle="modal" data-bs-target="#reviewModal">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" />
            </svg>
            {{ __('messages.re_evaluate') }} ({{ $reviewableResults->count() }})
        </div>
    </button>
</div>

<!-- Modal Đánh giá lại -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.reviews', $audit->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-3" id="reviewModalLabel">
                    <div class="rounded-3 bg-info bg-opacity-10 p-2 text-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" /></svg>
                    </div>
                    {{ __('messages.re_evaluation_result') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4 fs-6">{{ __('messages.audit_re_evaluate_photo') }}</p>

                @foreach($reviewableResults as $index => $result)
                <div class="card result-card shadow-none border mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-info bg-opacity-10 text-dark fw-bold border-0 p-3">
                        <div class="d-flex gap-3">
                            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 28px; height: 28px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="3"/><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/></svg>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fs-6 lh-sm">{{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}</div>
                                <div class="fw-normal small mt-1 text-muted">{{ __('messages.audit_improvement_reported_by') }} {{ $result->improver_name }}</div>
                                @if($result->is_completed)
                                <div class="mt-2 small text-success fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('messages.audit_department_reported_completion') }} {{ \Carbon\Carbon::parse($result->completed_at)->format('H:i d/m/Y') }}
                                </div>
                                @if(!empty($result->completion_note))
                                <div class="mt-2 small bg-success bg-opacity-10 text-dark p-2 rounded-3 border border-success border-opacity-25">
                                    <span class="fw-semibold text-success">{{ __('messages.audit_completion_note_label') }}:</span> {{ $result->completion_note }}
                                </div>
                                @endif
                                <div class="photo-grid mt-2">
                                    @foreach((array)$result->completion_image_path as $p_path)
                                    <div class="photo-thumb photo-thumb-sm shadow-sm" style="width: 80px; height: 80px">
                                        <a href="/{{ $p_path }}" target="_blank">
                                            <img src="/{{ $p_path }}" alt="Completion Proof">
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-dark">
                        <input type="hidden" name="reviews[{{ $index }}][result_id]" value="{{ $result->id }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.photo_after_improvement') }}</label>
                            <input type="file" name="reviews[{{ $index }}][review_image]" class="form-control border-0 bg-light rounded-3 py-2 px-3" accept="image/*" capture="environment">
                            <div class="form-text mt-2 text-muted fw-medium d-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                {{ __('messages.photo_after_improvement_hint') }}
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.audit_note') }}</label>
                            <textarea name="reviews[{{ $index }}][review_note]" class="form-control border-0 bg-light rounded-3 py-2 px-3" rows="2" placeholder="{{ __('messages.audit_note_placeholder') }}"></textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-3 h-48" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-info fw-bold px-4 rounded-3 h-48 text-white shadow-sm flex-grow-1">{{ __('messages.audit_confirm_completion_btn') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($isDepartmentUser && $completableResults->isNotEmpty())
<!-- Modal Xác nhận hoàn thiện (Dành cho bộ phận) -->
<div class="modal fade" id="confirmCompletionModal" tabindex="-1" aria-labelledby="confirmCompletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('audits.confirm_completion', $audit->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-3" id="confirmCompletionModalLabel">
                    <div class="rounded-3 bg-success bg-opacity-10 p-2 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    {{ __('messages.audit_view_improvement_report') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 rounded-4 mb-4 small">
                    {{ __('messages.audit_completion_proof_instruction') }}
                </div>

                @foreach($completableResults as $index => $result)
                <div class="card result-card shadow-none border mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-success bg-opacity-10 text-success fw-bold border-0 p-3">
                        <div class="d-flex gap-3">
                            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 28px; height: 28px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fs-6 lh-sm text-dark">{{ $result->criterion ? __($result->criterion->content) : __('messages.question_deleted') }}</div>
                                <div class="fw-normal small mt-1 text-muted">{{ __('messages.plan') }}: {{ $result->corrective_action }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <input type="hidden" name="completion[{{ $index }}][result_id]" value="{{ $result->id }}">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.audit_completion_note_label') }} <span class="text-danger">*</span></label>
                            <textarea name="completion[{{ $index }}][completion_note]" class="form-control border-0 bg-light rounded-3 py-2 px-3" rows="3" placeholder="{{ __('messages.audit_completion_note_placeholder') }}" required></textarea>
                            <div class="form-text mt-1 text-muted">{{ __('messages.audit_completion_note_instruction') }}</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.05em">{{ __('messages.audit_completion_proof_label', ['count' => 10]) }}</label>
                            <input type="file" name="completion[{{ $index }}][images][]" class="form-control border-0 bg-light rounded-3 py-2 px-3" accept="image/*" multiple capture="environment">
                            <div class="form-text mt-2 text-muted fw-medium d-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                {{ __('messages.audit_completion_proof_hint') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold px-4 rounded-3 h-48" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-success fw-bold px-4 rounded-3 h-48 text-white shadow-sm flex-grow-1">{{ __('messages.audit_submit_report_btn') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
