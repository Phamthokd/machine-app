@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.movement_history_title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold">{{ __('messages.movement_history_title') }}</h4>
    <a href="/movement-history/export?{{ http_build_query(request()->query()) }}" class="btn btn-success text-white shadow-sm tap d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span class="d-none d-sm-inline">{{ __('messages.movement_history_export') }}</span>
    </a>
</div>

<style>
    .card-modern {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
</style>

<div class="card-modern p-4 mb-4">
    <form method="GET" action="/movement-history" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('messages.filter_team') }}</label>
            <select name="department_id" class="form-select border-0 bg-light-subtle" style="background-color: #f8fafc; border-radius: 8px;">
                <option value="">-- {{ __('messages.all_depts') }} --</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id')==$dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('messages.from_date') }}</label>
            <input type="date" name="start_date" class="form-control border-0" style="background-color: #f8fafc; border-radius: 8px;" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ __('messages.to_date') }}</label>
            <input type="date" name="end_date" class="form-control border-0" style="background-color: #f8fafc; border-radius: 8px;" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-bold py-2 shadow-sm" style="border-radius: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                {{ __('messages.filter_button') }}
            </button>
            @if(request()->anyFilled(['department_id', 'start_date', 'end_date']))
            <a href="/movement-history" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 42px; border-radius: 8px;" title="{{ __('messages.clear_filter') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </a>
            @endif
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <!-- Desktop Table -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-4 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('messages.time_header') }}</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('messages.machine') }}</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('messages.from_dept') }}</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('messages.to_dept') }}</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('messages.mover') }}</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('messages.note') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">{{ $m->created_at->format('d/m/Y') }}</span>
                            <span class="text-secondary small">{{ $m->created_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="/m/{{ $m->machine->ma_thiet_bi }}" class="fw-bold text-primary text-decoration-none">
                            {{ $m->machine->ma_thiet_bi }}
                        </a>
                        <div class="small text-muted">{{Str::limit($m->machine->ten_thiet_bi, 20) }}</div>
                    </td>
                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $m->fromDepartment->name }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            <span class="badge bg-success bg-opacity-10 text-success">{{ $m->toDepartment->name }}</span>
                        </div>
                    </td>
                    <td>{{ $m->user->name ?? '—' }}</td>
                    <td class="text-secondary small fst-italic">{{ $m->note }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">{{ __('messages.no_movement_history') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none">
        @forelse($movements as $m)
        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <a href="/m/{{ $m->machine->ma_thiet_bi }}" class="fw-bold text-primary text-decoration-none fs-5">
                        {{ $m->machine->ma_thiet_bi }}
                    </a>
                    <div class="text-muted small">{{ $m->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <span class="badge bg-light text-dark border">{{ $m->user->name ?? '—' }}</span>
            </div>
            
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $m->fromDepartment->name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                <span class="badge bg-success bg-opacity-10 text-success">{{ $m->toDepartment->name }}</span>
            </div>
            
            @if($m->note)
                <div class="small text-secondary bg-light p-2 rounded">
                    📝 {{ $m->note }}
                </div>
            @endif
        </div>
        @empty
        <div class="text-center py-5 text-muted">{{ __('messages.no_movement_history') }}</div>
        @endforelse
    </div>

    @if($movements->hasPages())
    <div class="p-3">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
