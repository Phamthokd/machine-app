@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Lịch sử công trình')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Danh sách phiếu công trình</h4>
            <div class="text-secondary small">Quản lý và theo dõi lịch sử sửa chữa bộ phận công trình</div>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <a href="/repairs/contractor/export" class="btn btn-success text-white shadow-sm fw-bold d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Xuất Excel
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 1000px; font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr class="text-uppercase text-xs fw-bold">
                    <th class="py-3 px-3" style="width: 50px;">#</th>
                    <th class="py-3 px-3" style="width: 200px;">Thiết bị</th>
                    <th class="py-3 px-3">Sự cố</th>
                    <th class="py-3 px-3">Khắc phục</th>
                    <th class="py-3 px-3">Người hỗ trợ</th>
                    <th class="py-3 px-3" style="width: 180px;">Thời gian</th>
                    <th class="py-3 px-3" style="width: 150px;">Người tạo</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($repairs as $r)
                <tr>
                    <td class="px-3 text-secondary">{{ $r->code }}</td>
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
                        <div class="d-flex align-items-center gap-2">
                             <div class="avatar-sm rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                {{ substr($r->createdBy->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="fw-medium">{{ $r->createdBy->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-secondary">
                        <div class="mb-3 opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        Chưa có dữ liệu phiếu sửa công trình
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
