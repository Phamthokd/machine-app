@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.internal_audit'))

@section('content')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
        --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #10b981;
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }

    .btn-export:hover {
        background: #059669;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
    }

    .audit-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 3rem;
    }

    .audit-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .audit-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #3b82f6;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 3rem;
    }

    .card-dept {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-start-audit {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
        width: 100%;
        transition: all 0.2s;
    }

    .btn-start-audit:hover {
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        opacity: 0.9;
        color: white;
    }

    .history-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .score-badge {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.9rem;
    }
</style>

<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div>
        <h2 class="h3 mb-1 fw-bold text-dark">📋 {{ __('messages.internal_audit') }}</h2>
        <div class="text-muted small">{{ __('messages.manage_audits_subtitle') }}</div>
    </div>
    <a href="/audits/export" class="btn-export text-decoration-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" y1="15" x2="12" y2="3" />
        </svg>
        <span>{{ __('messages.export_excel') }}</span>
    </a>
</div>

@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 pulse">
    {{ session('success') }}
</div>
@endif

@if(auth()->check() && empty(auth()->user()->managed_department))
<div class="mb-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-baseline gap-2">
            <h4 class="h5 fw-bold text-dark m-0">{{ __('messages.start_new_audit') }}</h4>
            <span class="text-muted small">({{ count($templates) }} {{ __('messages.department') }})</span>
        </div>
        
        <!-- Bộ chọn Bộ phận -->
        <div class="d-flex align-items-center gap-2" style="min-width: 250px;">
            <label for="deptSelector" class="small fw-bold text-muted text-nowrap mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M22 3H2l8 9v11l4-6V12z"/></svg>
                {{ __('messages.filter_department') }}:
            </label>
            <select id="deptSelector" class="form-select form-select-sm border-0 shadow-sm rounded-3" style="background-color: #f1f5f9; font-weight: 600;">
                <option value="all">-- {{ __('messages.all') }} --</option>
                @foreach($templates->pluck('department_name')->unique() as $deptName)
                    <option value="{{ $deptName }}" {{ $deptName == 'BTP' ? 'selected' : '' }}>{{ __('messages.' . $deptName) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="audit-grid" id="auditTemplateGrid">
        @forelse($templates as $template)
        <div class="audit-card" data-dept="{{ $template->department_name }}">
            <div>
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <h5 class="card-title">{{ __($template->name) }}</h5>
                <div class="card-dept">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    {{ __('messages.' . $template->department_name) }}
                </div>
            </div>
            <a href="/audits/create?template_id={{ $template->id }}" class="btn btn-start-audit text-decoration-none text-center">
                {{ __('messages.start_new_audit') }}
            </a>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-4 shadow-sm w-100 grid-column-span-full">
            <p class="text-muted mb-0">{{ __('messages.no_active_templates') }}</p>
        </div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptSelector = document.getElementById('deptSelector');
    const auditCards = document.querySelectorAll('.audit-card');
    
    function filterCards(selectedDept) {
        auditCards.forEach(card => {
            if (selectedDept === 'all' || card.getAttribute('data-dept') === selectedDept) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (deptSelector) {
        // Lọc ngay khi tải trang dựa trên giá trị mặc định (BTP)
        filterCards(deptSelector.value);
        
        deptSelector.addEventListener('change', function() {
            filterCards(this.value);
        });
    }

    // Filter for Audit History
    const historyDeptSelector = document.getElementById('historyDeptSelector');
    const historyRows = document.querySelectorAll('.history-row');

    function filterHistory(selectedDept) {
        historyRows.forEach(row => {
            if (selectedDept === 'all' || row.getAttribute('data-dept') === selectedDept) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Filter for Audit History table (if it exists)
    // No longer using JS for history filter since it's server-side now
});
</script>
@endif

<div class="mb-5">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4">
            <h4 class="h5 mb-4 fw-bold text-dark d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M12 20v-6M9 20v-10M15 20v-2M3 20h18"/></svg>
                {{ __('messages.audit_history') }}
            </h4>

            <form action="{{ route('audits.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2 d-block">{{ __('messages.filter_department') }}</label>
                    <select name="history_dept" class="form-select form-select-sm border-0 shadow-sm rounded-3 py-2" style="background-color: #f1f5f9; font-weight: 600;">
                        <option value="all">-- {{ __('messages.all') }} --</option>
                        @foreach($templates->pluck('department_name')->unique() as $deptName)
                            <option value="{{ $deptName }}" {{ request('history_dept') == $deptName ? 'selected' : '' }}>{{ __('messages.' . $deptName) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2 d-block">{{ __('messages.start_date') }}</label>
                    <input type="date" name="start_date" class="form-select form-select-sm border-0 shadow-sm rounded-3 py-2" style="background-color: #f1f5f9; font-weight: 600;" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2 d-block">{{ __('messages.end_date') }}</label>
                    <input type="date" name="end_date" class="form-select form-select-sm border-0 shadow-sm rounded-3 py-2" style="background-color: #f1f5f9; font-weight: 600;" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100 py-2 rounded-3 shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        {{ __('messages.search') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="history-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>{{ __('messages.audit_template') }}</th>
                        <th width="100">{{ __('messages.score') }}</th>
                        <th>{{ __('messages.auditor') }}</th>
                        <th>{{ __('messages.time') }}</th>
                        <th width="200" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                    <tr>
                        <td class="text-muted small">#{{ $audit->id }}</td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-bold">{{ __($audit->template->name) }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">{{ __('messages.' . $audit->template->department_name) }}</span>
                                    @php
                                    $failedResults = $audit->results->where('is_passed', 0);
                                    $unrespondedResults = $failedResults->filter(fn($r) => is_null($r->department_agreement));
                                    $rejectedResultsPendingAudit = $failedResults->filter(fn($r) => $r->department_agreement === false && is_null($r->audit_rejection_decision));

                                    $userDept = auth()->user()->managed_department;
                                    $templateName = $audit->template->name;
                                    $isAdmin = auth()->user()->hasRole('admin');

                                    $isDepartmentUser = \Illuminate\Support\Facades\Auth::check() && (
                                        $isAdmin ||
                                        ($userDept === 'Bán thành phẩm' && ($templateName === 'Đánh giá bộ phận BTP' || $templateName === 'messages.audit_template_btp')) ||
                                        ($userDept === 'Phòng mẫu' && ($templateName === 'Đánh giá bộ phận Phòng mẫu' || $templateName === 'messages.audit_template_phong_mau')) ||
                                        ($userDept === 'Kiểm vải' && ($templateName === 'Đánh giá bộ phận Kiểm vải' || $templateName === 'messages.audit_template_kiem_vai')) ||
                                        (in_array($userDept, ['Xưởng 6 tầng 1', 'Xưởng 6 Tầng 1']) && ($templateName === 'Đánh giá Xưởng 6 tầng 1' || $templateName === 'messages.audit_template_x6_t1')) ||
                                        (in_array($userDept, ['Xưởng 6 tầng 2', 'Xưởng 6 Tầng 2']) && ($templateName === 'Đánh giá Xưởng 6 tầng 2' || $templateName === 'messages.audit_template_x6_t2')) ||
                                        ($userDept === 'Thêu' && ($templateName === 'Đánh giá bộ phận Thêu' || $templateName === 'messages.audit_template_theu')) ||
                                        ($userDept === 'May lập trình' && ($templateName === 'messages.audit_template_may_lap_trinh')) ||
                                        ($userDept === 'Kế toán' && ($templateName === 'messages.audit_template_ke_toan')) ||
                                        ($userDept === 'Sale + Đơn hàng' && ($templateName === 'messages.audit_template_sale_don_hang')) ||
                                        ($userDept === 'Kho vải + PL' && ($templateName === 'messages.audit_template_kho_vai_pl')) ||
                                        ($userDept === 'Nhà cắt' && ($templateName === 'messages.audit_template_nha_cat')) ||
                                        ($userDept === 'Nhà giặt' && ($templateName === 'messages.audit_template_nha_giat')) ||
                                        ($userDept === 'Thống kê tổng' && ($templateName === 'messages.audit_template_thong_ke_tong')) ||
                                        ($userDept === 'IE' && ($templateName === 'messages.audit_template_ie')) ||
                                        ($userDept === 'KHSX' && ($templateName === 'messages.audit_template_khsx'))
                                    );
                                    $canRespond = $isDepartmentUser && $unrespondedResults->isNotEmpty();

                                    $improveableResults = $failedResults->filter(function($r) {
                                        return $r->department_agreement === true ||
                                        ($r->department_agreement === false && $r->audit_rejection_decision === false);
                                    });
                                    $pendingImprovements = $improveableResults->filter(fn($r) => empty($r->root_cause));

                                    $hasImprovements = $audit->results->contains(fn($r) => !empty($r->improver_name));
                                    $unreviewed = $audit->results->filter(fn($r) => !empty($r->improver_name) && empty($r->reviewer_name));
                                    @endphp

                                    @if($unrespondedResults->isNotEmpty())
                                    <span class="status-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.audit_pending_department_feedback_badge') }}
                                    </span>
                                    @elseif($rejectedResultsPendingAudit->isNotEmpty())
                                    <span class="status-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.audit_pending_rejection_review_badge') }}
                                    </span>
                                    @elseif($pendingImprovements->isNotEmpty())
                                    <span class="status-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.audit_waiting_improvement_plan_badge') }}
                                    </span>
                                    @elseif($hasImprovements && $unreviewed->isEmpty())
                                    <span class="status-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.audit_reviewed_badge') }}
                                    </span>
                                    @elseif($unreviewed->isNotEmpty())
                                        @php
                                        $hasReachedDeadline = $unreviewed->every(function($r) {
                                            if (empty($r->improvement_deadline)) return true;
                                            return \Carbon\Carbon::now()->startOfDay()->gte(\Carbon\Carbon::parse($r->improvement_deadline)->startOfDay());
                                        });
                                        @endphp
                                        <span class="status-badge bg-{{ $hasReachedDeadline ? 'info' : 'warning' }} bg-opacity-10 text-{{ $hasReachedDeadline ? 'info' : 'warning' }} border border-{{ $hasReachedDeadline ? 'info' : 'warning' }} border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                            {{ $hasReachedDeadline ? __('messages.audit_improved_badge') : __('messages.audit_planned_badge') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="score-badge bg-{{ $audit->score == 100 ? 'success' : ($audit->score >= 80 ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $audit->score == 100 ? 'success' : ($audit->score >= 80 ? 'warning' : 'danger') }}">
                                {{ $audit->score }}%
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light rounded-circle p-2 text-center" style="width: 32px; height: 32px; line-height: 16px;">👤</div>
                                <span class="fw-medium">{{ $audit->auditor->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="text-muted small">
                            {{ $audit->created_at->format('H:i d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @if($canRespond)
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#respondModal_{{ $audit->id }}">
                                    {{ __('messages.audit_respond_btn') }}
                                </button>
                                @endif
                                <a href="/audits/{{ $audit->id }}" class="btn btn-sm btn-light border fw-medium px-3 text-primary">
                                    {{ __('messages.view_detail') }}
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                <form action="{{ route('audits.destroy', $audit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_audit') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="{{ __('messages.delete_audit') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>

                            @if($canRespond)
                            <!-- Modal Phản hồi lỗi -->
                            <div class="modal fade" id="respondModal_{{ $audit->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable text-start">
                                    <form action="{{ route('audits.agreements', $audit->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        @csrf
                                        <div class="modal-header bg-dark text-white border-0 py-3">
                                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                                </svg>
                                                {{ __('messages.audit_feedback_modal_title') }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <p class="text-secondary mb-4">{{ __('messages.audit_feedback_modal_desc') }}</p>

                                            @foreach($unrespondedResults as $index => $result)
                                            <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                                                <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 py-3 px-4">
                                                    <div class="d-flex gap-3">
                                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; flex-shrink: 0;">!</div>
                                                        <div>
                                                            <div class="fs-6">{{ $result->criterion ? $result->criterion->content : __('messages.question_deleted') }}</div>
                                                            <div class="fw-normal small mt-2 bg-white bg-opacity-50 p-2 rounded-3 text-dark">
                                                                <span class="fw-bold">{{ __('messages.issue') }}:</span> {{ $result->note }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold text-dark mb-2">{{ __('messages.audit_decision_label') }}</label>
                                                        <select class="form-select rounded-3 py-2 border-2" name="agreements[{{ $result->id }}][department_agreement]" required
                                                            onchange="document.getElementById('reject_reason_{{ $audit->id }}_{{ $result->id }}').style.display = this.value === '0' ? 'block' : 'none';">
                                                            <option value="" disabled selected>{{ __('messages.audit_choose_feedback') }}</option>
                                                            <option value="1">{{ __('messages.audit_agree_error_option') }}</option>
                                                            <option value="0">{{ __('messages.audit_dispute_error_option') }}</option>
                                                        </select>
                                                    </div>
                                                    <div id="reject_reason_{{ $audit->id }}_{{ $result->id }}" style="display: none;">
                                                        <label class="form-label fw-bold text-dark mb-2">{{ __('messages.audit_dispute_reason_only') }}</label>
                                                        <textarea class="form-control rounded-3 border-2" name="agreements[{{ $result->id }}][department_reject_reason]" rows="3" placeholder="{{ __('messages.audit_dispute_reason_placeholder') }}"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer bg-white border-0 p-4">
                                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                            <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm text-white text-uppercase rounded-3">{{ __('messages.audit_send_feedback_btn') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center">
                            <div class="text-muted d-flex flex-column align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <span>{{ __('messages.no_audit_history') }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($audits->hasPages())
        <div class="card-footer bg-white py-4 border-top">
            <div class="d-flex justify-content-center">
                {{ $audits->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection