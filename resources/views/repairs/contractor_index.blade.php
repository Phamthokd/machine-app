@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.construction_history'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">{{ __('messages.construction_history_list') }}</h4>
            <div class="text-secondary small">{{ __('messages.construction_history_subtitle') }}</div>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <a href="/repairs/contractor/export?{{ http_build_query(request()->query()) }}" class="btn btn-success text-white shadow-sm fw-bold d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            {{ __('messages.export_excel') }}
        </a>
    </div>
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
    <form method="GET" action="/repairs/contractor" class="row g-3 align-items-end">
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
            <a href="/repairs/contractor" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 42px; border-radius: 8px;" title="{{ __('messages.clear_filter') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </a>
            @endif
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 1000px; font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr class="text-uppercase text-xs fw-bold">
                    <th class="py-3 px-3" style="width: 60px;">{{ __('messages.stt') ?? 'STT' }}</th>
                    <th class="py-3 px-3" style="width: 200px;">{{ __('messages.machine_label') }}</th>
                    <th class="py-3 px-3">{{ __('messages.issue_label') }}</th>
                    <th class="py-3 px-3">{{ __('messages.fix_label') }}</th>
                    <th class="py-3 px-3">{{ __('messages.helper_label') }}</th>
                    <th class="py-3 px-3" style="width: 180px;">{{ __('messages.time') }}</th>
                    <th class="py-3 px-3" style="width: 150px;">{{ __('messages.repairer') ?? 'Người sửa' }}</th>
                    <th class="py-3 px-3" style="width: 150px;">{{ __('messages.reporter_label') }}</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($repairs as $r)
                <tr>
                    <td class="px-3 text-secondary">{{ (($repairs->currentPage() - 1) * $repairs->perPage()) + $loop->iteration }}</td>
                    <td class="px-3">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-primary">{{ $r->machine->ma_thiet_bi ?? '—' }}</span>
                            <span class="text-xs text-secondary">{{ $r->machine->ten_thiet_bi ?? '—' }}</span>
                            <span class="badge bg-light text-secondary mt-1 border" style="width: fit-content;">{{ $r->machine->department->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-3">
                        {{ $r->nguyen_nhan }}
                    </td>
                    <td class="px-3">
                        {{ $r->noi_dung_sua_chua }}
                    </td>
                    <td class="px-3">
                        @if($r->nguoi_ho_tro)
                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2 fw-normal">
                                🧑‍🔧 {{ $r->nguoi_ho_tro }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="px-3">
                        <div class="d-flex flex-column text-xs">
                              <div class="d-flex justify-content-between text-secondary">
                                 <span>Begin:</span>
                                 <span class="fw-bold text-success">{{ $r->started_at ? \Carbon\Carbon::parse($r->started_at)->format('H:i d/m') : '-' }}</span>
                              </div>
                              @if($r->started_at)
                                  @php
                                      $cWaitTime = \Carbon\Carbon::parse($r->created_at)->diffInMinutes(\Carbon\Carbon::parse($r->started_at));
                                  @endphp
                                  <div class="text-end">
                                      <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ __('messages.wait_time') }} {{ $cWaitTime }} {{ __('messages.minutes_unit') }}</span>
                                  </div>
                              @endif
                              <div class="d-flex justify-content-between text-secondary mt-1">
                                 <span>End:</span>
                                 <span class="fw-bold text-secondary">{{ $r->ended_at ? \Carbon\Carbon::parse($r->ended_at)->format('H:i d/m') : '-' }}</span>
                              </div>
                              @if($r->started_at && $r->ended_at)
                                  @php
                                      $cRepairTime = \Carbon\Carbon::parse($r->started_at)->diffInMinutes(\Carbon\Carbon::parse($r->ended_at));
                                  @endphp
                                  <div class="text-end">
                                      <span class="badge bg-light text-primary border" style="font-size: 0.65rem;">🛠️ {{ $cRepairTime }} {{ __('messages.minutes_unit') }}</span>
                                  </div>
                              @endif
                        </div>
                    </td>
                    <td class="px-3">
                        @if($r->mechanic)
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                     {{ substr($r->mechanic->name ?? 'M', 0, 1) }}
                                </div>
                                <span class="fw-medium text-primary">{{ $r->mechanic->name }}</span>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                             <div class="avatar-sm rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                 {{ substr($r->createdBy->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="fw-medium">{{ $r->createdBy->name ?? __('messages.unknown_user') }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-secondary">
                        <div class="mb-3 opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        {{ __('messages.no_construction_data') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($repairs->hasPages())
    <div class="px-4 py-3 border-top bg-light-subtle">
        {{ $repairs->links() }}
    </div>
    @endif
</div>
@endsection
