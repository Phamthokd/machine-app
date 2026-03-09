@extends('layouts.app-simple')
@section('title', __('messages.7s_inspection'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold">📋 {{ __('messages.7s_inspection') }}</h4>
    <a href="{{ route('seven-s.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span class="d-none d-sm-inline">{{ __('messages.7s_create_btn') }}</span>
        <span class="d-inline d-sm-none">{{ __('messages.add_new') }}</span>
    </a>
</div>

@if($records->isEmpty())
<div class="card border-0 shadow-sm rounded-4 text-center py-5">
    <div class="text-muted">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-3 opacity-25">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
        </svg>
        <p class="mb-0">{{ __('messages.7s_no_records') }}</p>
    </div>
</div>

@else

{{-- =================== DESKTOP TABLE (md and up) =================== --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>{{ __('messages.7s_department') }}</th>
                <th>{{ __('messages.7s_inspector') }}</th>
                <th>{{ __('messages.7s_score') }}</th>
                <th>{{ __('messages.7s_pass_rate') }}</th>
                <th>{{ __('messages.7s_improvement_status') }}</th>
                <th>{{ __('messages.7s_created_at') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            @php
            $hasE = $record->results->contains('grade', 'E');
            $nonBResults = $record->results->where('grade', '!=', 'B');
            $hasNonB = $nonBResults->isNotEmpty();
            $isFullyImproved = $hasNonB && $nonBResults->every(fn($r) => !empty($r->improvement_note));
            $pct = $record->max_score > 0 ? round(($record->score / $record->max_score) * 100) : 0;
            @endphp
            <tr class="{{ $hasE ? 'table-danger' : '' }}">
                <td class="text-muted small">{{ $record->id }}</td>
                <td><span class="badge bg-info text-dark">{{ $record->department }}</span></td>
                <td>{{ $record->inspector->name ?? '—' }}</td>
                <td class="fw-bold">
                    {{ $record->score }} / {{ $record->max_score }}
                    @if($hasE)
                    <span class="badge bg-danger ms-1 d-inline-flex align-items-center gap-1" title="{{ __('messages.7s_has_e_grade') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        {{ __('messages.7s_has_e_grade') }}
                    </span>
                    @endif
                </td>
                <td>
                    <span class="badge @if($pct >= 80) bg-success @elseif($pct >= 60) bg-warning text-dark @else bg-danger @endif">{{ $pct }}%</span>
                </td>
                <td>
                    @if(!$hasNonB)
                    @elseif($isFullyImproved)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        {{ __('messages.7s_improved_done') }}
                    </span>
                    @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ __('messages.7s_improvement_pending') }}
                    </span>
                    @endif
                </td>
                <td class="text-muted small">{{ $record->created_at->format('d/m/Y H:i') }}</td>
                <td><a href="{{ route('seven-s.show', $record->id) }}" class="btn btn-sm btn-outline-primary rounded-3">{{ __('messages.7s_view') }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- =================== MOBILE CARDS (below md) =================== --}}
<div class="d-flex flex-column gap-3 d-md-none">
    @foreach($records as $record)
    @php
    $hasE = $record->results->contains('grade', 'E');
    $nonBResults = $record->results->where('grade', '!=', 'B');
    $hasNonB = $nonBResults->isNotEmpty();
    $isFullyImproved = $hasNonB && $nonBResults->every(fn($r) => !empty($r->improvement_note));
    $pct = $record->max_score > 0 ? round(($record->score / $record->max_score) * 100) : 0;
    $pctColor = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
    @endphp
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden {{ $hasE ? 'border-danger border-2' : '' }}">
        {{-- Card header: department + score --}}
        <div class="card-body pb-0">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-info text-dark fs-6 px-3 py-2">{{ $record->department }}</span>
                    <span class="text-muted small">#{{ $record->id }}</span>
                    @if($hasE)
                    <span class="badge bg-danger d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        {{ __('messages.7s_has_e_grade') }}
                    </span>
                    @endif
                </div>
                {{-- Score circle --}}
                <div class="text-center flex-shrink-0">
                    <div class="fw-bold fs-5 text-{{ $pctColor }}">{{ $record->score }}<span class="text-muted small fw-normal">/{{ $record->max_score }}</span></div>
                    <span class="badge bg-{{ $pctColor }} px-2">{{ $pct }}%</span>
                </div>
            </div>

            {{-- Inspector + date --}}
            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                <span>👤 {{ $record->inspector->name ?? '—' }}</span>
                <span>🕒 {{ $record->created_at->format('d/m/Y H:i') }}</span>
            </div>

            {{-- Progress bar --}}
            <div class="progress mb-3" style="height:6px;border-radius:99px;">
                <div class="progress-bar bg-{{ $pctColor }}" style="width:{{ $pct }}%"></div>
            </div>
        </div>

        {{-- Card footer: improvement status + view button --}}
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 d-flex align-items-center justify-content-between gap-2">
            <div>
                @if(!$hasNonB)
                {{-- All B --}}
                @elseif($isFullyImproved)
                <span class="badge bg-success bg-opacity-10 text-success border border-success d-inline-flex align-items-center gap-1 px-2 py-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    {{ __('messages.7s_improved_done') }}
                </span>
                @else
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning d-inline-flex align-items-center gap-1 px-2 py-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ __('messages.7s_improvement_pending') }}
                </span>
                @endif
            </div>
            <a href="{{ route('seven-s.show', $record->id) }}" class="btn btn-sm btn-primary rounded-3 px-3">
                {{ __('messages.7s_view') }} →
            </a>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-3">{{ $records->links() }}</div>
@endif
@endsection