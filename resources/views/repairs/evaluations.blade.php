@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.repair_eval_stats'))

@section('content')
@php
    $labelMap = [
        'eval_response_time' => [
            'fast'  => ['label' => __('messages.eval_fast'),                   'class' => 'good'],
            'ok'    => ['label' => __('messages.eval_ok'), 'class' => 'ok'],
            'slow'  => ['label' => __('messages.eval_slow'),                    'class' => 'bad'],
        ],
        'eval_repair_speed' => [
            'fast'        => ['label' => __('messages.eval_fast_timely'),                     'class' => 'good'],
            'ok'          => ['label' => __('messages.eval_ok'),             'class' => 'ok'],
            'slow_affect' => ['label' => __('messages.eval_slow_affect'),    'class' => 'bad'],
        ],
        'eval_error_rate' => [
            'none'     => ['label' => __('messages.eval_none'),         'class' => 'good'],
            'few'      => ['label' => __('messages.eval_few'),            'class' => 'ok'],
            'frequent' => ['label' => __('messages.eval_frequent'),  'class' => 'bad'],
        ],
    ];

    function evalBadge(string $field, ?string $value, array $map): string {
        if (!$value || !isset($map[$field][$value])) return '<span class="badge bg-secondary">—</span>';
        $item = $map[$field][$value];
        $cls = match($item['class']) {
            'good' => 'bg-success',
            'ok'   => 'bg-warning text-dark',
            'bad'  => 'bg-danger',
            default => 'bg-secondary'
        };
        return "<span class=\"badge {$cls}\">{$item['label']}</span>";
    }
@endphp

<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold h3 mb-1">📊 {{ __('messages.repair_eval_stats') }}</h2>
        <div class="text-muted">{{ __('messages.repair_eval_summary') }}</div>
    </div>
</div>

{{-- Summary stats --}}
@php
    $total    = $evaluations->total();
    $goodResp = $evaluations->getCollection()->where('eval_response_time', 'fast')->count();
    $badSpeed = $evaluations->getCollection()->where('eval_repair_speed', 'slow_affect')->count();
    $noError  = $evaluations->getCollection()->where('eval_error_rate', 'none')->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-label">{{ __('messages.total_evaluations') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-good">
            <div class="stat-number">{{ $goodResp }}</div>
            <div class="stat-label">{{ __('messages.fast_response') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-bad">
            <div class="stat-number">{{ $badSpeed }}</div>
            <div class="stat-label">{{ __('messages.affect_production') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-good">
            <div class="stat-number">{{ $noError }}</div>
            <div class="stat-label">{{ __('messages.no_error_after_repair') }}</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <form method="GET" action="/repairs/evaluations" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem;">{{ __('messages.filter_team') }}</label>
            <select name="department_id" class="form-select">
                <option value="">-- {{ __('messages.all_depts') }} --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem;">{{ __('messages.from_date') }}</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem;">{{ __('messages.to_date') }}</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">🔍 {{ __('messages.filter_button') }}</button>
            @if(request()->anyFilled(['department_id','start_date','end_date']))
                <a href="/repairs/evaluations" class="btn btn-outline-secondary" title="{{ __('messages.clear_filter') }}">✕</a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem; min-width: 900px;">
            <thead class="bg-light text-secondary text-uppercase" style="font-size: 0.75rem;">
                <tr>
                    <th class="py-3 px-3">#</th>
                    <th class="py-3 px-3">{{ __('messages.machine_dept') }}</th>
                    <th class="py-3 px-3">{{ __('messages.repair_tech') }}</th>
                    <th class="py-3 px-3">{{ __('messages.repair_time_label') }}</th>
                    <th class="py-3 px-3">{{ __('messages.eval_response_time') }}</th>
                    <th class="py-3 px-3">{{ __('messages.eval_repair_speed') }}</th>
                    <th class="py-3 px-3">{{ __('messages.eval_error_rate') }}</th>
                    <th class="py-3 px-3">{{ __('messages.evaluator') }}</th>
                    <th class="py-3 px-3">{{ __('messages.eval_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $r)
                <tr>
                    <td class="px-3 text-secondary small">{{ $r->id }}</td>
                    <td class="px-3">
                        <div class="fw-bold text-primary">
                            <a href="/m/{{ $r->machine->ma_thiet_bi }}" class="text-decoration-none text-primary">
                                {{ $r->machine->ma_thiet_bi }}
                            </a>
                        </div>
                        <div class="text-muted small">{{ $r->machine->ten_thiet_bi }}</div>
                        <span class="badge bg-light text-secondary border mt-1">{{ $r->department->name ?? $r->machine->department->name ?? '—' }}</span>
                    </td>
                    <td class="px-3">
                        @if($r->mechanic)
                            <span class="fw-medium text-primary">{{ $r->mechanic->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="px-3 small" style="white-space: nowrap;">
                        @if($r->started_at && $r->ended_at)
                            @php $mins = \Carbon\Carbon::parse($r->started_at)->diffInMinutes(\Carbon\Carbon::parse($r->ended_at)); @endphp
                            <div>{{ \Carbon\Carbon::parse($r->started_at)->format('H:i d/m') }}</div>
                            <div class="text-muted">→ {{ \Carbon\Carbon::parse($r->ended_at)->format('H:i d/m') }}</div>
                            <span class="badge bg-light text-primary border">🛠️ {{ $mins }} {{ __('messages.minutes_unit') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="px-3">{!! evalBadge('eval_response_time', $r->eval_response_time, $labelMap) !!}</td>
                    <td class="px-3">{!! evalBadge('eval_repair_speed', $r->eval_repair_speed, $labelMap) !!}</td>
                    <td class="px-3">{!! evalBadge('eval_error_rate', $r->eval_error_rate, $labelMap) !!}</td>
                    <td class="px-3 small">{{ $r->createdBy->name ?? '—' }}</td>
                    <td class="px-3 small" style="white-space: nowrap;">{{ \Carbon\Carbon::parse($r->evaluated_at)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <div class="mb-2" style="font-size: 2.5rem;">📋</div>
                        {{ __('messages.no_eval_data') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($evaluations->hasPages())
    <div class="px-4 py-3 border-top bg-light-subtle">
        {{ $evaluations->links() }}
    </div>
    @endif
</div>

<style>
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 2px solid #e5e7eb;
    }
    .stat-card.stat-good { border-color: #bbf7d0; background: #f0fdf4; }
    .stat-card.stat-bad  { border-color: #fecaca; background: #fef2f2; }
    .stat-number { font-size: 2rem; font-weight: 800; color: #111827; }
    .stat-label  { font-size: 0.8rem; color: #6b7280; font-weight: 600; margin-top: 0.25rem; }
</style>
@endsection
