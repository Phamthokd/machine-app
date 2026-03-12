@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.internal_audit'))

@section('content')
<style>
    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #10b981;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-export:hover {
        background: #059669;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
</style>

<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="page-title h3 mb-1">📋 {{ __('messages.internal_audit') }}</h2>
        <div class="text-muted">{{ __('messages.manage_audits_subtitle') }}</div>
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
<div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
    {{ session('success') }}
</div>
@endif

@if(auth()->check() && empty(auth()->user()->managed_department))
<div class="row mb-5">
    <div class="col-12">
        <h4 class="h5 mb-3 fw-bold text-dark">{{ __('messages.start_new_audit') }}</h4>
        <div class="d-flex flex-wrap gap-3">
            @forelse($templates as $template)
            <div class="card border-0 shadow-sm rounded-3" style="min-width: 300px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded p-2 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 fw-bold">{{ __($template->name) }}</h5>
                            <span class="badge bg-light text-secondary border">{{ __('messages.department') }}: {{ __($template->department_name) }}</span>
                        </div>
                    </div>
                    <a href="/audits/create?template_id={{ $template->id }}" class="btn btn-primary w-100 fw-medium">
                        {{ __('messages.start_new_audit') }}
                    </a>
                </div>
            </div>
            @empty
            <div class="text-muted w-100 p-4 bg-white rounded-3 shadow-sm text-center">
                {{ __('messages.no_active_templates') }}
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <h4 class="h5 mb-3 fw-bold text-dark">{{ __('messages.audit_history') }}</h4>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary fw-semibold">ID</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">{{ __('messages.audit_template') }}</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">{{ __('messages.score') }}</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">{{ __('messages.auditor') }}</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">{{ __('messages.time') }}</th>
                            <th class="py-3 px-4 text-secondary fw-semibold text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td class="py-3 px-4 text-muted small">#{{ $audit->id }}</td>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    {{ __($audit->template->name) }}
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
                                        ($userDept === 'Thêu' && ($templateName === 'Đánh giá bộ phận Thêu' || $templateName === 'messages.audit_template_theu'))
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
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" title="Chờ bộ phận phản hồi lỗi">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                        </svg>
                                        {{ __('messages.audit_pending_department_feedback_badge') }}
                                    </span>
                                    @elseif($rejectedResultsPendingAudit->isNotEmpty())
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" title="{{ __('messages.audit_pending_rejection_review_tooltip') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 8v4l3 3" />
                                        </svg>
                                        {{ __('messages.audit_pending_rejection_review_badge') }}
                                    </span>
                                    @elseif($pendingImprovements->isNotEmpty())
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" title="{{ __('messages.audit_waiting_improvement_plan_tooltip') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                        {{ __('messages.audit_waiting_improvement_plan_badge') }}
                                    </span>
                                    @elseif($hasImprovements && $unreviewed->isEmpty())
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success" title="{{ __('messages.audit_reviewed_tooltip') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" class="me-1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                            <polyline points="22 4 12 14.01 9 11.01" />
                                        </svg>
                                        {{ __('messages.audit_reviewed_badge') }}
                                    </span>
                                    @elseif($unreviewed->isNotEmpty())
                                    @php
                                    $hasReachedDeadline = $unreviewed->every(function($r) {
                                    if (empty($r->improvement_deadline)) return true;
                                    return \Carbon\Carbon::now()->startOfDay()->gte(\Carbon\Carbon::parse($r->improvement_deadline)->startOfDay());
                                    });
                                    @endphp

                                    @if($hasReachedDeadline)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info" title="{{ __('messages.audit_ready_tooltip') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" class="me-1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="13" r="3" />
                                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                                        </svg>
                                        {{ __('messages.audit_improved_badge') }}
                                    </span>
                                    @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" title="{{ __('messages.audit_planned_tooltip') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                        {{ __('messages.audit_planned_badge') }}
                                    </span>
                                    @endif
                                    @endif
                                </div>
                                <div class="small text-muted">{{ __($audit->template->department_name) }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge {{ $audit->score == 100 ? 'bg-success' : ($audit->score >= 80 ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 text-{{ $audit->score == 100 ? 'success' : ($audit->score >= 80 ? 'warning' : 'danger') }} border border-{{ $audit->score == 100 ? 'success' : ($audit->score >= 80 ? 'warning' : 'danger') }}">
                                    {{ $audit->score }}%
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle p-1">👤</div>
                                    <span>{{ $audit->auditor->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-muted small">
                                {{ $audit->created_at->format('H:i d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($canRespond)
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#respondModal_{{ $audit->id }}">
                                        Phản hồi
                                    </button>
                                    @endif
                                    <a href="/audits/{{ $audit->id }}" class="btn btn-sm btn-light border text-primary" style="white-space: nowrap;">
                                        {{ __('messages.view_detail') }}
                                    </a>
                                    @if(auth()->user()->hasRole('admin'))
                                    <form action="{{ route('audits.destroy', $audit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_audit') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="{{ __('messages.delete_audit') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                <div class="modal fade" id="respondModal_{{ $audit->id }}" tabindex="-1" aria-labelledby="respondModalLabel_{{ $audit->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable text-start">
                                        <form action="{{ route('audits.agreements', $audit->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                                            @csrf
                                            <div class="modal-header bg-info bg-opacity-10 border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="respondModalLabel_{{ $audit->id }}">
                                                    <svg class="text-info" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                                    </svg>
                                                    {{ __('messages.audit_feedback_modal_title') }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                <p class="text-muted mb-4">{{ __('messages.audit_feedback_modal_desc') }}</p>

                                                @foreach($unrespondedResults as $index => $result)
                                                <div class="card bg-light border-0 shadow-sm mb-4 rounded-3 text-start">
                                                    <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 py-3">
                                                        <div class="d-flex gap-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-1">
                                                                <circle cx="12" cy="12" r="10" />
                                                                <line x1="12" y1="8" x2="12" y2="12" />
                                                                <line x1="12" y1="16" x2="12.01" y2="16" />
                                                            </svg>
                                                            <div>
                                                                <div class="fs-6">{{ $result->criterion ? $result->criterion->content : 'Hạng mục đã xóa' }}</div>
                                                                <div class="fw-normal small mt-1">Lỗi: {{ $result->note }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">{{ __('messages.audit_decision_label') }} <span class="text-danger">*</span></label>
                                                            <select class="form-select bg-white" name="agreements[{{ $result->id }}][department_agreement]" required
                                                                onchange="document.getElementById('reject_reason_{{ $audit->id }}_{{ $result->id }}').style.display = this.value === '0' ? 'block' : 'none';">
                                                                <option value="" disabled selected>{{ __('messages.audit_choose_feedback') }}</option>
                                                                <option value="1">{{ __('messages.audit_agree_error_option') }}</option>
                                                                <option value="0">Phản đối lỗi (Chờ Audit xem xét lại)</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3" id="reject_reason_{{ $audit->id }}_{{ $result->id }}" style="display: none;">
                                                            <label class="form-label fw-bold">Lý do phản đối <span class="text-danger">*</span></label>
                                                            <textarea class="form-control bg-white" name="agreements[{{ $result->id }}][department_reject_reason]" rows="3" placeholder="Nhập lý do tại sao bạn cho rằng đây không phải là lỗi..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                                <button type="submit" class="btn btn-info fw-bold px-4 shadow-sm text-white text-uppercase">Gửi phản hồi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-muted">{{ __('messages.no_audit_history') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($audits->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $audits->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection