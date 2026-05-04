@extends('layouts.app-simple')
@section('title', __('messages.create_ticket') . ' - ' . $machine->ma_thiet_bi)

@section('content')
<style>
    :root {
        --primary-color: #4f46e5;
        --bg-app: #f8fafc;
    }

    body {
        background-color: var(--bg-app) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Machine Summary Card */
    .machine-summary {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .machine-summary .label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .machine-summary .value {
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Form Styles */
    .form-section {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label {
        font-weight: 500;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        padding: 12px 16px;
        border-color: #e2e8f0;
        background-color: #fff;
        font-size: 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    textarea.form-control {
        min-height: 100px;
    }

    /* Sticky Bottom Bar */
    .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 16px;
        border-top: 1px solid #e2e8f0;
        z-index: 100;
        padding-bottom: max(16px, env(safe-area-inset-bottom));
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .btn-submit {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 1rem;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit:active {
        transform: scale(0.98);
    }

    /* Spacer for sticky footer */
    .footer-spacer {
        height: 100px;
    }

    /* Unevaluated Modal */
    .modal-unevaluated .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }
    .modal-unevaluated .modal-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        padding: 20px 24px 16px;
    }
    .modal-unevaluated .modal-body {
        padding: 24px;
    }
    .modal-unevaluated .modal-footer {
        border: none;
        padding: 0 24px 24px;
        gap: 12px;
    }
    .unevaluated-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        background: #fef2f2;
        border: 2px solid #fca5a5;
        border-radius: 50%;
        font-size: 1.5rem;
        font-weight: 800;
        color: #dc2626;
        margin-bottom: 12px;
    }
</style>

{{-- Modal cảnh báo phiếu chưa đánh giá (chỉ hiện với tổ trưởng có >= 3 phiếu) --}}
@hasrole('team_leader')
@if($unevaluatedCount >= 3)
<div class="modal fade modal-unevaluated" id="unevaluatedWarningModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="unevaluatedWarningModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <h5 class="modal-title mb-0 fw-bold" id="unevaluatedWarningModalLabel">{{ __('messages.need_evaluate_title') }}</h5>
                </div>
            </div>
            <div class="modal-body text-center">
                <div class="unevaluated-count-badge mx-auto">{{ $unevaluatedCount }}</div>
                <h6 class="fw-bold text-danger mb-2">{{ __('messages.unevaluated_warning_msg', ['count' => $unevaluatedCount]) }}</h6>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    {{ __('messages.unevaluated_desc') }}
                </p>
            </div>
            <div class="modal-footer flex-column">
                <a href="/repairs" class="btn btn-danger w-100 fw-bold rounded-pill py-2 d-flex align-items-center justify-content-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    {{ __('messages.go_to_history') }}
                </a>
                <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2" data-bs-dismiss="modal">
                    {{ __('messages.continue_create') }}
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('unevaluatedWarningModal'));
        modal.show();
    });
</script>
@endif
@endhasrole

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/m/{{ $machine->ma_thiet_bi }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
        {{ __('messages.back') }}
    </a>
    <h4 class="mb-0 fw-bold">{{ __('messages.create_ticket') }}</h4>
</div>

<form method="POST" action="/repairs">
    @csrf
    <input type="hidden" name="machine_id" value="{{ $machine->id }}">
    <input type="hidden" name="department_id" value="{{ $machine->department->id }}">

    <!-- Machine Info -->
    <div class="machine-summary">
        <div class="row g-3">
            <div class="col-6">
                <div class="label">{{ __('messages.machine_code') }}</div>
                <div class="value">{{ $machine->ma_thiet_bi }}</div>
            </div>
            <div class="col-6">
                <div class="label">{{ __('messages.dept') }}</div>
                <div class="value">{{ $machine->department->name }}</div>
            </div>
            <div class="col-12">
                <div class="label">{{ __('messages.machine_name') }}</div>
                <div class="value">{{ $machine->ten_thiet_bi }}</div>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
        <div class="fw-bold mb-2">⚠️ {{ __('messages.check_errors') }}</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Issue Details -->
    <div class="form-section">
        <div class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
            {{ __('messages.repair_info_section') }}
        </div>

        @hasrole('contractor')
        <!-- Simplified Form for Contractor -->
        <div class="mb-3">
            <label class="form-label">{{ __('messages.damage_reason_label') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" name="nguyen_nhan" placeholder="VD: Đứt chỉ, kẹt ổ, gãy kim..." required>{{ old('nguyen_nhan') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('messages.repair_content_label') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Thay kim, chỉnh ổ, vệ sinh..." required>{{ old('noi_dung_sua_chua') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('messages.helper_label') }} ({{ __('messages.optional_label') }})</label>
            <select class="form-select" name="nguoi_ho_tro">
                <option value="">{{ __('messages.select_helper') }}</option>
                @foreach($contractors as $c)
                <option value="{{ $c->name }}" @selected(old('nguoi_ho_tro')==$c->name)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Hidden fields for Contractor -->
        <input type="hidden" name="ma_hang" value="N/A">
        <input type="hidden" name="cong_doan" value="N/A">
        <input type="hidden" name="endline_qc_name" value="N/A">

        @else
        @hasrole('team_leader')
        <!-- Type Selection -->
        <div class="mb-3">
            <label class="form-label">{{ __('messages.request_type') }} <span class="text-danger">*</span></label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="type_mechanic" value="mechanic" checked>
                    <label class="form-check-label" for="type_mechanic">
                        🔧 {{ __('messages.type_repair') }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="type_contractor" value="contractor">
                    <label class="form-check-label" for="type_contractor">
                        🏗 {{ __('messages.type_construction') }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Simplified Form for Team Leader (Request Only) -->
        <div class="mb-3">
            <label class="form-label">{{ __('messages.issue_desc_label') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" name="nguyen_nhan" placeholder="VD: Máy kêu to, không chạy, đứt chỉ..." rows="4" required>{{ old('nguyen_nhan') }}</textarea>
        </div>

        <!-- Hidden fields for Team Leader -->
        <input type="hidden" name="ma_hang" value="N/A">
        <input type="hidden" name="cong_doan" value="N/A">
        <input type="hidden" name="noi_dung_sua_chua" value="N/A"> <!-- Will be updated later by mechanic -->
        <input type="hidden" name="endline_qc_name" value="N/A">
        @else
        <!-- Standard Form for Repair Tech / Admin -->
        @if(request('type') == 'maintenance')
        <input type="hidden" name="ma_hang" value="{{ __('messages.maintenance_label') }}">
        <input type="hidden" name="cong_doan" value="{{ __('messages.maintenance_label') }}">
        <input type="hidden" name="nguyen_nhan" value="{{ __('messages.maintenance_label') }}">

        <div class="mb-3">
            <label class="form-label">{{ __('messages.maintenance_fix_label') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Tra dầu, lau chùi, kiểm tra định kỳ..." required>{{ old('noi_dung_sua_chua', __('messages.maintenance_label')) }}</textarea>
        </div>
        @else
        <div class="mb-3">
            <label class="form-label">{{ __('messages.code_label') }} <span class="text-danger">*</span></label>
            <input class="form-control" name="ma_hang" value="{{ old('ma_hang') }}" placeholder="VD: H1-12345" required>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('messages.step_label') }} <span class="text-danger">*</span></label>
            <input class="form-control" name="cong_doan" value="{{ old('cong_doan') }}" placeholder="VD: Tra gấu" required>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('messages.damage_reason_label') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" name="nguyen_nhan" placeholder="VD: Đứt chỉ, kẹt ổ, gãy kim..." required>{{ old('nguyen_nhan') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('messages.repair_content_label') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Thay kim, chỉnh ổ, vệ sinh..." required>{{ old('noi_dung_sua_chua') }}</textarea>
        </div>
        @endif
        @endhasrole
        @endhasrole
    </div>

    @unlessrole('contractor|team_leader')
    <!-- Time & Personnel (Only for Non-Contractors & Non-TeamLeaders) -->
    <div class="form-section">
        <div class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
            {{ __('messages.time_personnel') }}
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('messages.start_time_label') }} <span class="text-danger">*</span></label>
            @php $startedAtValue = old('started_at', now(config('app.timezone'))->format('Y-m-d\TH:i')); @endphp
            <input type="hidden" name="started_at" value="{{ $startedAtValue }}">
            <div class="form-control bg-light text-muted d-flex align-items-center gap-2" style="pointer-events:none;user-select:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                {{ \Carbon\Carbon::parse($startedAtValue)->format('d/m/Y H:i') }}
                <span class="ms-auto badge bg-secondary bg-opacity-25 text-secondary small">🔒</span>
            </div>
        </div>

        <!-- Static QC Fields -->
        <div class="mb-3">
            <label class="form-label">{{ __('messages.endline_qc_label') }} <span class="text-secondary small fw-normal ms-1">({{ __('messages.optional_label') }})</span></label>
            <select class="form-select" name="endline_qc_name">
                <option value="">{{ __('messages.select_endline_qc') }}</option>
                <option value="Ánh" @selected(old('endline_qc_name')=='Ánh' )>Ánh</option>
                <option value="Thuỷ" @selected(old('endline_qc_name')=='Thuỷ' )>Thuỷ</option>
                <option value="Vân Anh" @selected(old('endline_qc_name')=='Vân Anh' )>Vân Anh</option>
                <option value="Thanh" @selected(old('endline_qc_name')=='Thanh' )>Thanh</option>
            </select>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label class="form-label">Inline QC (Triumph)</label>
                <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional_label') }}</span>
            </div>
            <select class="form-select" name="inline_qc_name">
                <option value="">{{ __('messages.select_inline_qc') }}</option>
                <option value="Sinh" @selected(old('inline_qc_name')=='Sinh' )>Sinh</option>
                <option value="Chiêm" @selected(old('inline_qc_name')=='Chiêm' )>Chiêm</option>
            </select>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label class="form-label">{{ __('messages.qa_supervisor') }} (Triumph)</label>
                <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional_label') }}</span>
            </div>
            <select class="form-select" name="qa_supervisor_name">
                <option value="" selected>{{ __('messages.select_qa') }}</option>
                <option value="Tuyen">Tuyen</option>
            </select>
        </div>

    </div>
    @endunlessrole

    @hasrole('team_leader')
    <!-- Hidden started_at for Team Leader (Auto Now) -->
    <input type="hidden" name="started_at" value="{{ now()->format('Y-m-d\\TH:i') }}">
    @endhasrole

    <!-- Spacer to ensure content isn't hidden behind footer -->
    <div class="footer-spacer"></div>

    <!-- Submit Button -->
    <div class="fixed-bottom container p-3 bg-white border-top" style="max-width: 600px;">
        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2">
            @hasrole('team_leader')
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            {{ __('messages.send_request_btn') }}
            @else
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
            </svg>
            {{ __('messages.save_ticket_btn') }}
            @endhasrole
        </button>
    </div>

</form>

<script>
    const startedAtField = document.querySelector('[data-auto-now]');
    if (startedAtField && !startedAtField.value) {
        const now = new Date();
        const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 16);
        startedAtField.value = local;
    }
</script>
@endsection