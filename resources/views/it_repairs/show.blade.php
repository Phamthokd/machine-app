@extends('layouts.app-simple')
@section('title', 'Chi tiết phiếu IT – ' . $ticket->code)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('it-repairs.index') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Quay lại
        </a>
        <h4 class="mb-0 fw-bold">🖥️ {{ $ticket->code }}</h4>
        <span class="badge bg-{{ $ticket->statusColor() }} bg-opacity-15 text-{{ $ticket->statusColor() }} border border-{{ $ticket->statusColor() }} border-opacity-25 rounded-pill px-3 py-2 fs-6">
            {{ $ticket->statusLabel() }}
        </span>
    </div>
    @if(auth()->user()->isAdminUser())
    <form action="{{ route('it-repairs.destroy', $ticket->id) }}" method="POST"
          onsubmit="return confirm('Bạn có chắc muốn xóa phiếu này?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Xóa phiếu</button>
    </form>
    @endif
</div>

<div class="row g-4">
    {{-- Main info --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">{{ $ticket->title }}</h5>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Loại sự cố</div>
                        <span class="badge bg-light text-dark border fs-6 fw-normal">{{ $ticket->issueTypeLabel() }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Mức ưu tiên</div>
                        <span class="badge bg-{{ $ticket->priorityColor() }} bg-opacity-15 text-{{ $ticket->priorityColor() }} border border-{{ $ticket->priorityColor() }} border-opacity-25 rounded-pill px-3">
                            {{ $ticket->priorityLabel() }}
                        </span>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Ngày tạo</div>
                        <div class="fw-semibold small">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($ticket->resolved_at)
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Đã giải quyết lúc</div>
                        <div class="fw-semibold small text-success">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Mô tả chi tiết</div>
                    <div class="bg-light rounded-3 p-3">{{ $ticket->description }}</div>
                </div>

                @if($ticket->location)
                <div class="mb-3">
                    <div class="text-muted small mb-1">📍 Vị trí / Phòng</div>
                    <div class="fw-semibold">{{ $ticket->location }}</div>
                </div>
                @endif

                @if($ticket->images && count($ticket->images) > 0)
                <div class="mb-3">
                    <div class="text-muted small mb-2">Ảnh đính kèm</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($ticket->images as $img)
                        <a href="{{ Storage::url($img) }}" target="_blank">
                            <img src="{{ Storage::url($img) }}" class="rounded-3 img-thumbnail" style="width:100px;height:100px;object-fit:cover;">
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($ticket->resolution_note)
                <div class="alert border-0 rounded-3" style="background:#d1fae5;">
                    <div class="fw-bold text-success mb-1">✅ Nội dung giải quyết</div>
                    <div>{{ $ticket->resolution_note }}</div>
                    @if($ticket->resolver)
                    <div class="text-muted small mt-2">— {{ $ticket->resolver->name }}</div>
                    @endif
                    @if($ticket->nguoi_ho_tro)
                    <div class="text-muted small mt-1">🧑‍💻 Người hỗ trợ: <strong class="text-dark">{{ $ticket->nguoi_ho_tro }}</strong></div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar: reporter info + update status --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <div class="fw-bold mb-3">👤 Thông tin người báo</div>
                <div class="mb-2">
                    <div class="text-muted small">Người tạo phiếu</div>
                    <div class="fw-semibold">{{ $ticket->reporter?->name ?? '—' }}</div>
                </div>
                @if($ticket->department)
                <div class="mb-2">
                    <div class="text-muted small">Bộ phận</div>
                    <div class="fw-semibold">{{ $ticket->department }}</div>
                </div>
                @endif
                <div>
                    <div class="text-muted small">Ngày tạo</div>
                    <div class="fw-semibold">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        @if(auth()->user()->canManageItRepairs() && !$ticket->isResolved())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="fw-bold mb-3">🔧 Cập nhật trạng thái</div>
                <form action="{{ route('it-repairs.update', $ticket->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Trạng thái mới</label>
                        <select name="status" class="form-select rounded-3" required>
                            <option value="pending"     @selected($ticket->status === 'pending')>⏳ Chờ xử lý</option>
                            <option value="in_progress" @selected($ticket->status === 'in_progress')>🔄 Đang xử lý</option>
                            <option value="resolved"    @selected($ticket->status === 'resolved')>✅ Đã giải quyết</option>
                            <option value="closed"      @selected($ticket->status === 'closed')>🔒 Đã đóng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nội dung giải quyết</label>
                        <textarea name="resolution_note" class="form-control rounded-3" rows="3"
                                  placeholder="Mô tả cách giải quyết sự cố...">{{ old('resolution_note', $ticket->resolution_note) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2">
                        Cập nhật
                    </button>
                </form>
            </div>
        </div>
        @elseif($ticket->isResolved())
        <div class="card border-0 rounded-4" style="background:#d1fae5;">
            <div class="card-body p-4 text-center">
                <div class="fs-2 mb-2">✅</div>
                <div class="fw-bold text-success">Phiếu đã được giải quyết</div>
                @if($ticket->resolver)
                <div class="text-muted small mt-1">Bởi {{ $ticket->resolver->name }}</div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
