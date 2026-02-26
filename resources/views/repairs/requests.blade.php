@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.repair_requests_title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">{{ __('messages.repair_requests_title') }}</h4>
            <div class="text-secondary small">{{ __('messages.repair_requests_subtitle') }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px; font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr class="text-uppercase text-xs fw-bold">
                    <th class="py-3 px-3">{{ __('messages.machine_code_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.machine_name_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.dept_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.issue_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.reporter_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.report_time_header') }}</th>
                    <th class="py-3 px-3 text-end">{{ __('messages.action_header') }}</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($requests as $r)
                <tr>
                    <td class="px-3 fw-bold text-primary">{{ $r->machine->ma_thiet_bi ?? '—' }}</td>
                    <td class="px-3">{{ $r->machine->ten_thiet_bi ?? '—' }}</td>
                    <td class="px-3">
                         <span class="badge bg-light text-secondary border">{{ $r->machine->department->name ?? '—' }}</span>
                    </td>
                    <td class="px-3 text-wrap" style="max-width: 300px;">
                        {{ $r->nguyen_nhan }}
                    </td>
                     <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                             <div class="avatar-sm rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                {{ substr($r->createdBy->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="fw-medium">{{ $r->createdBy->name ?? __('messages.unknown_user') }}</span>
                        </div>
                    </td>
                    <td class="px-3 text-secondary">
                        {{ $r->created_at->format('H:i d/m/Y') }}
                    </td>
                    <td class="px-3 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            @role('team_leader|audit')
                                <span class="badge bg-light text-secondary border px-3 py-2">Chỉ xem</span>
                            @else
                                <a href="/repairs/{{ $r->id }}/edit" class="btn btn-sm btn-primary rounded-pill px-3">
                                    {{ __('messages.process_btn') }}
                                </a>
                            @endrole

                            @role('admin')
                            <form action="/repairs/{{ $r->id }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phiếu báo sửa này không?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                    Xóa
                                </button>
                            </form>
                            @endrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-secondary">
                        <div class="mb-3 opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </div>
                        {{ __('messages.no_requests') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
