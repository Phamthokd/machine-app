@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Phiếu IT')

@section('content')
<style>
    .stat-card { border-radius: 16px; padding: 1.25rem 1.5rem; color: white; }
    .ticket-row:hover { background: #f8faff; }
    .badge-priority-low      { background: #94a3b8; }
    .badge-priority-medium   { background: #38bdf8; }
    .badge-priority-high     { background: #f59e0b; }
    .badge-priority-urgent   { background: #ef4444; }
    .status-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-0">🖥️ Phiếu IT</h4>
        <p class="text-muted small mb-0">Quản lý sự cố và yêu cầu hỗ trợ IT</p>
    </div>
    @if(auth()->user()->canManageItRepairs())
    <a href="{{ route('it-repairs.create') }}" class="btn btn-primary fw-bold d-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tạo phiếu mới
    </a>
    @endif
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <div class="fs-2 fw-bold">{{ $stats['pending'] }}</div>
            <div class="small opacity-85">Chờ xử lý</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
            <div class="fs-2 fw-bold">{{ $stats['in_progress'] }}</div>
            <div class="small opacity-85">Đang xử lý</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);">
            <div class="fs-2 fw-bold">{{ $stats['resolved'] }}</div>
            <div class="small opacity-85">Đã giải quyết</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
            <div class="fs-2 fw-bold">{{ $stats['total'] }}</div>
            <div class="small opacity-85">Tổng phiếu</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('it-repairs.index') }}" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Trạng thái</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="">Tất cả</option>
                    <option value="pending"     @selected(request('status') === 'pending')>Chờ xử lý</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>Đang xử lý</option>
                    <option value="resolved"    @selected(request('status') === 'resolved')>Đã giải quyết</option>
                    <option value="closed"      @selected(request('status') === 'closed')>Đã đóng</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Loại sự cố</label>
                <select name="issue_type" class="form-select form-select-sm rounded-3">
                    <option value="">Tất cả</option>
                    <option value="computer" @selected(request('issue_type') === 'computer')>Máy tính</option>
                    <option value="network"  @selected(request('issue_type') === 'network')>Mạng / Internet</option>
                    <option value="printer"  @selected(request('issue_type') === 'printer')>Máy in</option>
                    <option value="software" @selected(request('issue_type') === 'software')>Phần mềm</option>
                    <option value="other"    @selected(request('issue_type') === 'other')>Khác</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Mức ưu tiên</label>
                <select name="priority" class="form-select form-select-sm rounded-3">
                    <option value="">Tất cả</option>
                    <option value="urgent" @selected(request('priority') === 'urgent')>🔴 Khẩn cấp</option>
                    <option value="high"   @selected(request('priority') === 'high')>🟠 Cao</option>
                    <option value="medium" @selected(request('priority') === 'medium')>🔵 Bình thường</option>
                    <option value="low"    @selected(request('priority') === 'low')>⚪ Thấp</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Từ ngày</label>
                <input type="date" name="date_from" class="form-control form-control-sm rounded-3" value="{{ request('date_from') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Đến ngày</label>
                <input type="date" name="date_to" class="form-control form-control-sm rounded-3" value="{{ request('date_to') }}">
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 flex-grow-1">Lọc</button>
                <a href="{{ route('it-repairs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">Xóa</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8faff;">
                <tr>
                    <th class="px-4 py-3 fw-semibold text-muted small">Mã phiếu</th>
                    <th class="py-3 fw-semibold text-muted small">Tiêu đề</th>
                    <th class="py-3 fw-semibold text-muted small">Loại sự cố</th>
                    <th class="py-3 fw-semibold text-muted small">Ưu tiên</th>
                    <th class="py-3 fw-semibold text-muted small">Người báo</th>
                    <th class="py-3 fw-semibold text-muted small">Trạng thái</th>
                    <th class="py-3 fw-semibold text-muted small">Ngày tạo</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr class="ticket-row">
                    <td class="px-4 py-3">
                        <span class="font-monospace text-primary fw-bold small">{{ $ticket->code }}</span>
                    </td>
                    <td class="py-3">
                        <div class="fw-semibold">{{ Str::limit($ticket->title, 50) }}</div>
                        @if($ticket->location)
                            <div class="text-muted small">📍 {{ $ticket->location }}</div>
                        @endif
                    </td>
                    <td class="py-3">
                        <span class="badge bg-light text-dark border">{{ $ticket->issueTypeLabel() }}</span>
                    </td>
                    <td class="py-3">
                        <span class="badge badge-priority-{{ $ticket->priority }} text-white">{{ $ticket->priorityLabel() }}</span>
                    </td>
                    <td class="py-3">
                        <div class="small">{{ $ticket->reporter?->name ?? '—' }}</div>
                        @if($ticket->department)
                            <div class="text-muted" style="font-size:0.75rem;">{{ $ticket->department }}</div>
                        @endif
                    </td>
                    <td class="py-3">
                        <span class="badge bg-{{ $ticket->statusColor() }} bg-opacity-15 text-{{ $ticket->statusColor() }} border border-{{ $ticket->statusColor() }} border-opacity-25 rounded-pill px-3">
                            <span class="status-dot bg-{{ $ticket->statusColor() }}"></span>
                            {{ $ticket->statusLabel() }}
                        </span>
                    </td>
                    <td class="py-3 text-muted small">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 pe-3">
                        <a href="{{ route('it-repairs.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary rounded-3">Xem</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="fs-1 mb-2">🖥️</div>
                        <div>Chưa có phiếu IT nào</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div class="card-footer bg-transparent border-0 py-3 px-4">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
