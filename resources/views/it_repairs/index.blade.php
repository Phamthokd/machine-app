@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Danh sách phiếu IT')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Danh sách phiếu IT</h4>
            <div class="text-secondary small">Quản lý và theo dõi lịch sử sửa chữa bộ phận IT</div>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        @if(auth()->user()->canManageItRepairs())
        <a href="{{ route('it-repairs.create') }}" class="btn btn-primary text-white shadow-sm fw-bold d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tạo phiếu mới
        </a>
        @endif
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

{{-- Filter Card --}}
<div class="card-modern p-4 mb-4">
    <form method="GET" action="{{ route('it-repairs.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">LỌC THEO BỘ PHẬN</label>
            <select name="department_id" class="form-select border-0" style="background-color: #f8fafc; border-radius: 8px;">
                <option value="">-- Tất cả các bộ phận --</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">TỪ NGÀY</label>
            <input type="date" name="start_date" class="form-control border-0" style="background-color: #f8fafc; border-radius: 8px;" value="{{ request('start_date', request('date_from')) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">ĐẾN NGÀY</label>
            <input type="date" name="end_date" class="form-control border-0" style="background-color: #f8fafc; border-radius: 8px;" value="{{ request('end_date', request('date_to')) }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-bold py-2 shadow-sm" style="border-radius: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                Lọc dữ liệu
            </button>
            @if(request()->anyFilled(['department_id', 'start_date', 'end_date', 'date_from', 'date_to', 'status', 'issue_type']))
            <a href="{{ route('it-repairs.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 42px; border-radius: 8px;" title="Xóa lọc">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 1000px; font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr class="text-uppercase text-xs fw-bold">
                    <th class="py-3 px-3" style="width: 60px;">STT</th>
                    <th class="py-3 px-3" style="width: 200px;">THIẾT BỊ</th>
                    <th class="py-3 px-3">SỰ CỐ</th>
                    <th class="py-3 px-3">KHẮC PHỤC</th>
                    <th class="py-3 px-3">NGƯỜI HỖ TRỢ</th>
                    <th class="py-3 px-3" style="width: 180px;">THỜI GIAN</th>
                    <th class="py-3 px-3" style="width: 150px;">NGƯỜI SỬA</th>
                    <th class="py-3 px-3" style="width: 150px;">NGƯỜI TẠO</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($tickets as $t)
                <tr onclick="window.location='{{ route('it-repairs.show', $t->id) }}'" style="cursor: pointer;">
                    <td class="px-3 text-secondary">{{ (($tickets->currentPage() - 1) * $tickets->perPage()) + $loop->iteration }}</td>
                    <td class="px-3">
                        <div class="d-flex flex-column">
                            @if($t->machine)
                                <span class="fw-bold text-primary">{{ $t->machine->ma_thiet_bi }}</span>
                                <span class="text-xs text-secondary">{{ $t->machine->ten_thiet_bi }}</span>
                                <span class="badge bg-light text-secondary mt-1 border" style="width: fit-content;">{{ $t->machine->department->name ?? $t->department ?? 'IT' }}</span>
                            @else
                                <span class="fw-bold text-primary">{{ $t->code }}</span>
                                <span class="text-xs text-secondary">{{ $t->issueTypeLabel() }}</span>
                                <span class="badge bg-light text-secondary mt-1 border" style="width: fit-content;">{{ $t->department ?? 'IT' }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-3">
                        {{ $t->description ?? $t->title ?? 'N/A' }}
                    </td>
                    <td class="px-3">
                        {{ $t->resolution_note ?? 'N/A' }}
                    </td>
                    <td class="px-3">
                        @if($t->nguoi_ho_tro)
                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2 fw-normal">
                                🧑‍💻 {{ $t->nguoi_ho_tro }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="px-3">
                        <div class="d-flex flex-column text-xs">
                            <div class="d-flex justify-content-between text-secondary">
                                <span>Begin:</span>
                                <span class="fw-bold text-success">{{ $t->started_at ? \Carbon\Carbon::parse($t->started_at)->format('H:i d/m') : \Carbon\Carbon::parse($t->created_at)->format('H:i d/m') }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-secondary mt-1">
                                <span>End:</span>
                                <span class="fw-bold text-secondary">{{ $t->ended_at ? \Carbon\Carbon::parse($t->ended_at)->format('H:i d/m') : ($t->resolved_at ? \Carbon\Carbon::parse($t->resolved_at)->format('H:i d/m') : '-') }}</span>
                            </div>
                            @if(($t->started_at || $t->created_at) && ($t->ended_at || $t->resolved_at))
                                @php
                                    $start = $t->started_at ?? $t->created_at;
                                    $end = $t->ended_at ?? $t->resolved_at;
                                    $cRepairTime = abs(round(\Carbon\Carbon::parse($start)->diffInMinutes(\Carbon\Carbon::parse($end))));
                                @endphp
                                <div class="text-end mt-1">
                                    <span class="badge bg-light text-primary border" style="font-size: 0.65rem;">🛠️ {{ $cRepairTime }} phút</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-3">
                        @if($t->resolver)
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                    {{ mb_substr($t->resolver->name ?? 'M', 0, 1) }}
                                </div>
                                <span class="fw-medium text-primary">{{ $t->resolver->name }}</span>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                {{ mb_substr($t->reporter->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="fw-medium">{{ $t->reporter->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-secondary">
                        <div class="mb-3 opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        Chưa có dữ liệu phiếu IT
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($tickets->hasPages())
    <div class="px-4 py-3 border-top bg-light-subtle">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
