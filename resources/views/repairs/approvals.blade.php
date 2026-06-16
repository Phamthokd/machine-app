@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.repair_approvals') . ' - ' . __('messages.pending_approval_status'))

@section('content')
<style>
    .approval-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 50px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
    }
    .card-approval {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: box-shadow 0.2s;
    }
    .card-approval:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.13);
    }
    .info-chip {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.82rem;
        color: #475569;
    }
    .info-chip span {
        font-weight: 600;
        color: #1e293b;
    }
    .btn-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: opacity 0.2s, transform 0.1s;
    }
    .btn-approve:hover { opacity: 0.9; color: white; transform: translateY(-1px); }
    .btn-approve:active { transform: scale(0.97); }
    .btn-reject {
        background: white;
        color: #ef4444;
        border: 2px solid #fca5a5;
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .btn-reject:hover { background: #fef2f2; border-color: #ef4444; color: #ef4444; transform: translateY(-1px); }
    .empty-state {
        padding: 80px 20px;
        text-align: center;
        color: #94a3b8;
    }
    .empty-state-icon {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 2rem;
    }
    .stat-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 16px;
        padding: 20px 24px;
        color: white;
        margin-bottom: 24px;
    }
    /* Reject Modal */
    .modal-reject .modal-content { border-radius: 20px; border: none; overflow: hidden; }
    .modal-reject .modal-header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; }
</style>

{{-- Stat Header --}}
<div class="stat-card d-flex align-items-center gap-4 flex-wrap">
    <div>
        <div style="font-size:0.85rem;opacity:0.8;text-transform:uppercase;letter-spacing:0.05em;">{{ __('messages.pending_approval_count') }}</div>
        <div style="font-size:2.5rem;font-weight:800;line-height:1.1;">{{ $totalPending }}</div>
    </div>
    <div class="ms-auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
    </div>
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">🔑 {{ __('messages.repair_approvals') }}</h4>
            <div class="text-secondary small">{{ __('messages.repair_approvals_subtitle') }}</div>
        </div>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    ✅ {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    ⚠️ {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Content --}}
@forelse($pendingApprovals as $ticket)
<div class="card card-approval mb-3">
    <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            {{-- Left: Info --}}
            <div class="flex-grow-1" style="min-width:240px;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="approval-badge">⏳ {{ __('messages.pending_approval_status') }}</span>
                    <code class="text-secondary small">{{ $ticket->code }}</code>
                </div>
                <h5 class="fw-bold mb-1" style="font-size:1rem;">
                    🖥 {{ $ticket->machine->ten_thiet_bi ?? 'N/A' }}
                    <span class="text-muted fw-normal fs-6">— {{ $ticket->machine->ma_thiet_bi ?? '' }}</span>
                </h5>
                <div class="text-secondary small mb-3">
                    🏭 {{ $ticket->machine->department->name ?? ($ticket->department->name ?? 'Không rõ tổ') }}
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-auto">
                        <div class="info-chip">
                            👤 {{ __('messages.reporter_label') }}: <span>{{ $ticket->createdBy->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="info-chip">
                            🕐 {{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="info-chip">
                            📋 {{ __('messages.type_construction') }}
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background:#f8fafc;border-left:3px solid #f59e0b;">
                    <div class="text-xs text-secondary fw-bold mb-1" style="font-size:0.75rem;text-transform:uppercase;">{{ __('messages.issue_label') }} / {{ __('messages.damage_reason') }}</div>
                    <p class="mb-0" style="font-size:0.92rem;">{{ $ticket->nguyen_nhan }}</p>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="d-flex flex-column gap-2" style="min-width:160px;">
                {{-- Approve --}}
                <form method="POST" action="{{ route('repairs.approve', $ticket) }}">
                    @csrf
                    <button type="submit" class="btn-approve w-100 d-flex align-items-center justify-content-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        {{ __('messages.approve_btn') }}
                    </button>
                </form>

                {{-- Reject --}}
                <button type="button" class="btn-reject w-100 d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ticket->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    {{ __('messages.reject_btn') }}
                </button>

                {{-- View detail --}}
                <a href="/repairs/{{ $ticket->id }}" class="btn btn-light w-100 rounded-3 d-flex align-items-center justify-content-center gap-1" style="font-size:0.85rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{ __('messages.view_detail') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal for this ticket --}}
<div class="modal fade modal-reject" id="rejectModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('repairs.reject', $ticket) }}" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ __('messages.reject_modal_title', ['code' => $ticket->code]) }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary mb-3" style="font-size:0.9rem;">
                    {{ __('messages.reject_modal_desc') }}
                </p>
                <div class="mb-0">
                    <label class="form-label fw-bold">{{ __('messages.reject_reason_label') }} <span class="text-danger">*</span></label>
                    <textarea name="approval_note" class="form-control rounded-3" rows="4"
                        placeholder="{{ __('messages.reject_reason_placeholder') }}" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 gap-3">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-bold" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-danger px-4 rounded-3 fw-bold flex-grow-1">
                    {{ __('messages.reject_confirm_btn') }}
                </button>
            </div>
        </form>
    </div>
</div>

@empty
<div class="empty-state">
    <div class="empty-state-icon">✅</div>
    <h5 class="fw-bold text-success mb-2">{{ __('messages.no_pending_approvals') }}</h5>
    <p class="text-secondary">{{ __('messages.no_pending_approvals_desc') }}</p>
    <a href="/dashboard" class="btn btn-light rounded-pill px-5 mt-3">{{ __('messages.back_to_home') }}</a>
</div>
@endforelse

{{-- Pagination --}}
@if($pendingApprovals->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $pendingApprovals->links() }}
</div>
@endif

@endsection
