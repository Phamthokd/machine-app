@extends('layouts.app-simple')
@section('title', __('messages.complete_repair_ticket'))

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="/repair-requests" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            {{ __('messages.back') }}
        </a>
        <h3 class="fw-bold mb-1">{{ __('messages.accept_repair') }}</h3>
        <p class="text-secondary mb-0">{{ __('messages.complete_repair_info') }} #{{ $repair->code }}</p>
    </div>

    <!-- Machine Info Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">{{ __('messages.machine_code') }}</div>
                    <div class="h3 fw-bold mb-1">{{ $machine->ma_thiet_bi }}</div>
                    <div class="fw-medium text-white-50">{{ $machine->ten_thiet_bi }}</div>
                </div>
                <div class="text-end">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">{{ __('messages.department') }}</div>
                    <div class="h5 fw-bold mb-0">{{ $machine->department->name }}</div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div class="fw-bold">{{ __('messages.please_check_again') }}</div>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/repairs/{{ $repair->id }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Issue Details -->
        <div class="form-section">
            <div class="section-title">
                {{ __('messages.issue_details') }}
            </div>
            
            @if($repair->type == 'contractor')
                <!-- CONTRACTOR FORM -->
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.damage_cause') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="nguyen_nhan" required>{{ old('nguyen_nhan', $repair->nguyen_nhan) }}</textarea>
                    <div class="form-text">{{ __('messages.contractor_cause_hint') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.repair_content') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="noi_dung_sua_chua" placeholder="{{ __('messages.contractor_repair_hint') }}" required>{{ old('noi_dung_sua_chua') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.supporter_optional') }}</label>
                    <select class="form-select" name="nguoi_ho_tro">
                        <option value="">{{ __('messages.select_supporter') }}</option>
                        @foreach($contractors as $c)
                            <option value="{{ $c->name }}" @selected(old('nguoi_ho_tro', $repair->nguoi_ho_tro) == $c->name)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- MECHANIC FORM -->
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.product_code') }} <span class="text-danger">*</span></label>
                    <input class="form-control" name="ma_hang" value="{{ old('ma_hang', $repair->ma_hang == 'N/A' ? '' : $repair->ma_hang) }}" placeholder="{{ __('messages.product_code_hint') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.process_stage') }} <span class="text-danger">*</span></label>
                    <input class="form-control" name="cong_doan" value="{{ old('cong_doan', $repair->cong_doan == 'N/A' ? '' : $repair->cong_doan) }}" placeholder="{{ __('messages.process_stage_hint') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.damage_cause') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="nguyen_nhan" required>{{ old('nguyen_nhan', $repair->nguyen_nhan) }}</textarea>
                    <div class="form-text">{{ __('messages.mechanic_cause_hint') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.repair_content') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="noi_dung_sua_chua" placeholder="{{ __('messages.mechanic_repair_hint') }}" required>{{ old('noi_dung_sua_chua') }}</textarea>
                </div>
            @endif
        </div>

        <!-- Time & Personnel -->
        <div class="form-section">
            <div class="section-title">
                {{ __('messages.time_personnel') }}
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.start_time_label') }} <span class="text-danger">*</span></label>
                <input
                class="form-control"
                type="datetime-local"
                name="started_at"
                value="{{ old('started_at', now()->format('Y-m-d\\TH:i')) }}"
                required>
                <div class="form-text">{{ __('messages.start_time_hint') }}</div>
            </div>

            @if($repair->type != 'contractor')
                <!-- Static QC Fields (Only for Mechanics) -->
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.endline_qc_triumph') }}</label>
                    <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional') }}</span>
                    <select class="form-select" name="endline_qc_name">
                        <option value="">{{ __('messages.select_endline_qc') }}</option>
                        <option value="Ánh" @selected(old('endline_qc_name') == 'Ánh')>Ánh</option>
                        <option value="Thuỷ" @selected(old('endline_qc_name') == 'Thuỷ')>Thuỷ</option>
                        <option value="Vân Anh" @selected(old('endline_qc_name') == 'Vân Anh')>Vân Anh</option>
                        <option value="Thanh" @selected(old('endline_qc_name') == 'Thanh')>Thanh</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label">{{ __('messages.inline_qc_triumph') }}</label>
                        <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional') }}</span>
                    </div>
                    <select class="form-select" name="inline_qc_name">
                        <option value="">{{ __('messages.select_inline_qc') }}</option>
                        <option value="Sinh" @selected(old('inline_qc_name') == 'Sinh')>Sinh</option>
                        <option value="Chiên" @selected(old('inline_qc_name') == 'Chiên')>Chiên</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label">{{ __('messages.qa_supervisor_triumph') }}</label>
                        <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional') }}</span>
                    </div>
                    <select class="form-select" name="qa_supervisor_name">
                    <option value="" selected>{{ __('messages.select_qa') }}</option>
                        <option value="Terence" >Terence</option>
                    </select>
                </div>
            @endif
        </div>

        <!-- Spacer -->
        <div class="footer-spacer"></div>

        <!-- Submit Button -->
        <div class="fixed-bottom container p-3 bg-white border-top" style="max-width: 600px;">
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                {{ mb_strtoupper(__('messages.complete_ticket')) }}
            </button>
        </div>
    </form>
</div>

<style>
.form-section {
    background: #ffffff;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
}
.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #4b5563;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
}
.footer-spacer {
    height: 100px;
}
</style>
@endsection
