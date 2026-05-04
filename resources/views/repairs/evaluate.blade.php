@extends('layouts.app-simple')
@section('title', __('messages.eval_quality_title'))

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="/repairs" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6" />
            </svg>
            {{ __('messages.back') }}
        </a>
        <h3 class="fw-bold mb-1">⭐ {{ __('messages.eval_quality_title') }}</h3>
        <p class="text-secondary mb-0">{{ __('messages.repair_ticket_label') }} #{{ $repair->code }}</p>
    </div>

    {{-- Machine info card --}}
    <div class="eval-card info-card mb-4">
        <div class="info-row-item">
            <span class="info-key">🔧 {{ __('messages.machine') }}</span>
            <span class="fw-bold">{{ $repair->machine->ma_thiet_bi }} – {{ $repair->machine->ten_thiet_bi }}</span>
        </div>
        <div class="info-row-item">
            <span class="info-key">🏭 {{ __('messages.department') }}</span>
            <span>{{ $repair->department->name ?? $repair->machine->department->name ?? '—' }}</span>
        </div>
        @if($repair->mechanic)
        <div class="info-row-item">
            <span class="info-key">🛠 {{ __('messages.repair_tech') }}</span>
            <span class="text-primary fw-medium">{{ $repair->mechanic->name }}</span>
        </div>
        @endif
        <div class="info-row-item">
            <span class="info-key">✅ {{ __('messages.status_done') }}</span>
            <span>{{ \Carbon\Carbon::parse($repair->ended_at)->format('H:i d/m/Y') }}</span>
        </div>
    </div>

    @if($repair->evaluated_at)
    <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
        <div class="fw-bold mb-1">✅ {{ __('messages.eval_status_done') }} {{ \Carbon\Carbon::parse($repair->evaluated_at)->format('H:i d/m/Y') }}</div>
        <div class="small text-muted">{{ __('messages.eval_complete_msg') }}</div>
    </div>
    @else
    <form method="POST" action="/repairs/{{ $repair->id }}/evaluate">
        @csrf

        {{-- Question 1 --}}
        <div class="eval-card mb-3">
            <div class="q-number">{{ __('messages.question_prefix') }} 1</div>
            <div class="q-title">{{ __('messages.eval_q1_title') }}</div>

            @error('eval_response_time')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="options-grid">
                <label class="option-card option-good">
                    <input type="radio" name="eval_response_time" value="fast" {{ old('eval_response_time') == 'fast' ? 'checked' : '' }}>
                    <div class="option-icon">⚡</div>
                    <div class="option-label">{{ __('messages.eval_fast') }}</div>
                </label>
                <label class="option-card option-ok">
                    <input type="radio" name="eval_response_time" value="ok" {{ old('eval_response_time') == 'ok' ? 'checked' : '' }}>
                    <div class="option-icon">✓</div>
                    <div class="option-label">{{ __('messages.eval_ok') }}</div>
                </label>
                <label class="option-card option-bad">
                    <input type="radio" name="eval_response_time" value="slow" {{ old('eval_response_time') == 'slow' ? 'checked' : '' }}>
                    <div class="option-icon">🐢</div>
                    <div class="option-label">{{ __('messages.eval_slow') }}</div>
                </label>
            </div>
        </div>

        {{-- Question 2 --}}
        <div class="eval-card mb-3">
            <div class="q-number">{{ __('messages.question_prefix') }} 2</div>
            <div class="q-title">{{ __('messages.eval_q2_title') }}</div>

            @error('eval_repair_speed')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="options-grid">
                <label class="option-card option-good">
                    <input type="radio" name="eval_repair_speed" value="fast" {{ old('eval_repair_speed') == 'fast' ? 'checked' : '' }}>
                    <div class="option-icon">🚀</div>
                    <div class="option-label">{{ __('messages.eval_fast_timely') }}</div>
                </label>
                <label class="option-card option-ok">
                    <input type="radio" name="eval_repair_speed" value="ok" {{ old('eval_repair_speed') == 'ok' ? 'checked' : '' }}>
                    <div class="option-icon">✓</div>
                    <div class="option-label">{{ __('messages.eval_ok') }}</div>
                </label>
                <label class="option-card option-bad">
                    <input type="radio" name="eval_repair_speed" value="slow_affect" {{ old('eval_repair_speed') == 'slow_affect' ? 'checked' : '' }}>
                    <div class="option-icon">⚠️</div>
                    <div class="option-label">{{ __('messages.eval_slow_affect') }}</div>
                </label>
            </div>
        </div>

        {{-- Question 3 --}}
        <div class="eval-card mb-4">
            <div class="q-number">{{ __('messages.question_prefix') }} 3</div>
            <div class="q-title">{{ __('messages.eval_q3_title') }}</div>

            @error('eval_error_rate')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="options-grid">
                <label class="option-card option-good">
                    <input type="radio" name="eval_error_rate" value="none" {{ old('eval_error_rate') == 'none' ? 'checked' : '' }}>
                    <div class="option-icon">🎯</div>
                    <div class="option-label">{{ __('messages.eval_none') }}</div>
                </label>
                <label class="option-card option-ok">
                    <input type="radio" name="eval_error_rate" value="few" {{ old('eval_error_rate') == 'few' ? 'checked' : '' }}>
                    <div class="option-icon">⚡</div>
                    <div class="option-label">{{ __('messages.eval_few') }}</div>
                </label>
                <label class="option-card option-bad">
                    <input type="radio" name="eval_error_rate" value="frequent" {{ old('eval_error_rate') == 'frequent' ? 'checked' : '' }}>
                    <div class="option-icon">❌</div>
                    <div class="option-label">{{ __('messages.eval_frequent') }}</div>
                </label>
            </div>
        </div>

        <div class="footer-spacer"></div>

        <div class="fixed-bottom container p-3 bg-white border-top" style="max-width: 600px;">
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                {{ __('messages.submit_eval_btn') }}
            </button>
        </div>
    </form>
    @endif
</div>

<style>
    .eval-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .info-card {
        border-left: 4px solid #6366f1;
    }

    .info-row-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.45rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.95rem;
    }

    .info-row-item:last-child { border-bottom: none; }

    .info-key {
        color: #6b7280;
        min-width: 100px;
        font-size: 0.85rem;
    }

    .q-number {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6366f1;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.4rem;
    }

    .q-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1.1rem;
        line-height: 1.4;
    }

    .options-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .option-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 0.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.875rem;
        cursor: pointer;
        transition: all 0.18s ease;
        text-align: center;
        user-select: none;
        background: #fafafa;
    }

    .option-card input[type="radio"] { display: none; }

    .option-card:hover { border-color: #a5b4fc; background: #eef2ff; }

    .option-card input[type="radio"]:checked ~ .option-icon,
    .option-card:has(input[type="radio"]:checked) {
        font-weight: 700;
    }

    .option-card:has(input[type="radio"]:checked).option-good {
        border-color: #22c55e;
        background: #f0fdf4;
        box-shadow: 0 0 0 3px #bbf7d0;
    }

    .option-card:has(input[type="radio"]:checked).option-ok {
        border-color: #f59e0b;
        background: #fffbeb;
        box-shadow: 0 0 0 3px #fde68a;
    }

    .option-card:has(input[type="radio"]:checked).option-bad {
        border-color: #ef4444;
        background: #fef2f2;
        box-shadow: 0 0 0 3px #fecaca;
    }

    .option-icon { font-size: 1.75rem; }

    .option-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        line-height: 1.3;
    }

    .footer-spacer { height: 100px; }

    @media (max-width: 480px) {
        .options-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
