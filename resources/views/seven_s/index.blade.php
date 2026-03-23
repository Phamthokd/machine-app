@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.7s_inspection'))

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
        <h2 class="h3 mb-1 fw-bold text-dark">{{ __('messages.7s_inspection') }}</h2>
        <div class="text-muted small">{{ __('messages.7s_manage_subtitle') ?? 'Quản lý các đợt kiểm tra và đánh giá 7S các bộ phận' }}</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
    {{ session('success') }}
</div>
@endif

@if(auth()->check() && empty(auth()->user()->managed_department))
<div class="mb-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-baseline gap-2">
            <h4 class="h5 fw-bold text-dark m-0">{{ __('messages.7s_create_btn') }}</h4>
            <span class="text-muted small">({{ count($templates) }} {{ __('messages.department') }})</span>
        </div>

        <!-- Department selector -->
        <div class="d-flex align-items-center gap-2" style="min-width: 250px;">
            <label for="deptSelector" class="small fw-bold text-muted text-nowrap mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                    <path d="M22 3H2l8 9v11l4-6V12z" />
                </svg>
                {{ __('messages.filter_department') }}:
            </label>
            <select id="deptSelector" class="form-select form-select-sm border-0 shadow-sm rounded-3" style="background-color: #f1f5f9; font-weight: 600;">
                <option value="all">-- {{ __('messages.all') }} --</option>
                @foreach($templates as $deptName)
                <option value="{{ $deptName }}">{{ __('messages.' . $deptName) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="audit-grid" id="auditTemplateGrid">
        @foreach($templates as $deptName)
        <div class="audit-card" data-dept="{{ $deptName }}">
            <div>
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <rect x="8" y="13" width="8" height="2"></rect>
                        <rect x="8" y="17" width="8" height="2"></rect>
                    </svg>
                </div>
                <h5 class="card-title">{{ __('messages.7s_inspection') }} {{ __('messages.' . $deptName) }}</h5>
                <div class="card-dept">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    {{ __('messages.' . $deptName) }}
                </div>
            </div>
            <a href="{{ route('seven-s.create', ['department' => $deptName]) }}" class="btn btn-start-audit text-decoration-none text-center">
                {{ __('messages.7s_create_btn') }}
            </a>
        </div>
        @endforeach
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
            deptSelector.addEventListener('change', function() {
                filterCards(this.value);
            });
        }
    });
</script>
@endif

<div class="mb-5">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4">
            <h4 class="h5 mb-4 fw-bold text-dark d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                    <path d="M12 20v-6M9 20v-10M15 20v-2M3 20h18" />
                </svg>
                {{ __('messages.7s_history') ?? 'Lịch sử kiểm tra 7S' }}
            </h4>

            <form action="{{ route('seven-s.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2 d-block">{{ __('messages.filter_department') }}</label>
                    <select name="history_dept" class="form-select form-select-sm border-0 shadow-sm rounded-3 py-2" style="background-color: #f1f5f9; font-weight: 600;">
                        <option value="all">-- {{ __('messages.all') }} --</option>
                        @foreach($templates as $deptName)
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
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
                        <th>{{ __('messages.7s_department') }}</th>
                        <th width="100">{{ __('messages.7s_score') }}</th>
                        <th>{{ __('messages.7s_inspector') }}</th>
                        <th>{{ __('messages.7s_created_at') }}</th>
                        <th width="100" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    @php
                    $failedResults = $record->results->where('grade', '!=', 'B');
                    $unrespondedResults = $failedResults->filter(fn($r) => is_null($r->department_agreement));
                    $rejectedResultsPendingAudit = $failedResults->filter(fn($r) => $r->department_agreement === false && is_null($r->auditor_rejection_decision));

                    $userDept = auth()->user()->managed_department;
                    $auditDept = $record->department;
                    $isAdmin = auth()->user()->hasRole('admin');

                    $userDeptMapped = \App\Models\AuditTemplate::normalizeDepartmentName($userDept);
                    $auditDeptMapped = \App\Models\AuditTemplate::normalizeDepartmentName($auditDept);

                    $isDepartmentUser = \Illuminate\Support\Facades\Auth::check() && (
                        !empty($userDeptMapped) && !empty($auditDeptMapped) && $userDeptMapped === $auditDeptMapped
                    );
                    $canRespond = $isDepartmentUser && $unrespondedResults->isNotEmpty();

                    $improveableResults = $failedResults->filter(function($r) {
                        return $r->department_agreement === true ||
                        ($r->department_agreement === false && $r->auditor_rejection_decision === false);
                    });
                    $pendingImprovements = $improveableResults->filter(fn($r) => empty($r->improvement_note));

                    $hasImprovements = $record->results->contains(fn($r) => !empty($r->improvement_note));
                    $unreviewed = $record->results->filter(fn($r) => !empty($r->improvement_note) && empty($r->reviewer_id));
                    $anyRejected = $record->results->contains(fn($r) => $r->review_status === 'rejected');
                    $hasE = $record->results->contains('grade', 'E');
                    
                    $pct = $record->max_score > 0 ? round(($record->score / $record->max_score) * 100) : 0;
                    @endphp
                    <tr class="{{ $hasE ? 'table-danger bg-opacity-10' : '' }}">
                        <td class="text-muted small">#{{ $record->id }}</td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-bold">{{ __('messages.' . $record->department) }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    @if($unrespondedResults->isNotEmpty())
                                    <span class="status-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.7s_status_pending_feedback') }}
                                    </span>
                                    @elseif($rejectedResultsPendingAudit->isNotEmpty())
                                    <span class="status-badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.7s_status_pending_dispute_review') }}
                                    </span>
                                    @elseif($pendingImprovements->isNotEmpty())
                                    <span class="status-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.7s_status_pending_improvement') }}
                                    </span>
                                    @elseif($anyRejected)
                                    <span class="status-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.7s_status_rejected') }}
                                    </span>
                                    @elseif($unreviewed->isNotEmpty())
                                    <span class="status-badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.7s_status_pending_review') }}
                                    </span>
                                    @elseif($failedResults->isNotEmpty())
                                    <span class="status-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        {{ __('messages.7s_status_approved') }}
                                    </span>
                                    @else
                                    <span class="status-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-none" style="padding: 2px 8px;">
                                        100% OK
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="score-badge bg-{{ $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger') }}">
                                {{ $pct }}%
                            </div>
                            <div class="text-center small text-muted mt-1">{{ $record->score }}/{{ $record->max_score }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21a8 8 0 1 0-16 0"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <span class="fw-medium">{{ $record->inspector->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="text-muted small">
                            {{ $record->created_at->format('H:i d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @if($canRespond)
                                <a href="{{ route('seven-s.show', $record->id) }}" class="btn btn-sm btn-info text-white shadow-sm fw-bold px-3" title="{{ __('messages.audit_respond_btn') ?? 'Phản hồi' }}">
                                    {{ __('messages.audit_respond_btn') ?? 'Phản hồi' }}
                                </a>
                                @endif
                                <a href="{{ route('seven-s.show', $record->id) }}" class="btn btn-sm btn-light border text-primary px-2" title="{{ __('messages.view_detail') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                <form action="{{ route('seven-s.destroy', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_audit') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="{{ __('messages.delete') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center">
                            <div class="text-muted d-flex flex-column align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <span>{{ __('messages.7s_no_records') }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="card-footer bg-white py-4 border-top">
            <div class="d-flex justify-content-center">
                {{ $records->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection