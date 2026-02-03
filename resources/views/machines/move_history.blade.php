@extends('layouts.app-simple')
@section('title', 'Lịch sử chuyển tổ')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold">Lịch sử chuyển tổ</h4>
    <a href="/movement-history/export" class="btn btn-success text-white shadow-sm tap d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span class="d-none d-sm-inline">Xuất Excel</span>
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <!-- Desktop Table -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-4 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Thời gian</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Thiết bị</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Từ tổ</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Đến tổ</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Người chuyển</th>
                    <th class="py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ghi chú</th>
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
                    <td colspan="6" class="text-center py-5 text-muted">Chưa có lịch sử chuyển tổ nào.</td>
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
        <div class="text-center py-5 text-muted">Chưa có lịch sử chuyển tổ nào.</div>
        @endforelse
    </div>

    @if($movements->hasPages())
    <div class="p-3">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
