@php
    $maxWidth = '100%';
@endphp
@extends('layouts.app-simple')
@section('title', 'Chi Tiết Phiếu Khách #' . $ticket->id)

@section('content')
@php $currentUser = auth()->user(); @endphp

<style>
    .cursor-pointer { cursor: pointer; }
    .guest-mobile-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .btn-now {
        font-size: 0.75rem;
        padding: 4px 8px;
        white-space: nowrap;
    }
    @media (max-width: 768px) {
        .card-header, .card-body {
            padding: 1rem !important;
        }
        .form-control-sm, .form-select-sm {
            font-size: 15px !important;
            min-height: 42px;
        }
    }
</style>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('visitor-tickets.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 d-flex align-items-center gap-1 text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            <span class="d-none d-sm-inline">Danh sách</span>
        </a>
        <h4 class="mb-0 fw-bold fs-5 fs-md-4">
            Phiếu Khách <span class="text-primary">#{{ $ticket->id }}</span>
        </h4>
    </div>

    {{-- Trạng thái --}}
    @if($ticket->isOpen())
        <span class="badge rounded-pill px-3 py-2 fw-semibold shadow-sm" style="background:#dcfce7;color:#15803d;font-size:.85rem;">
            ● Đang mở
        </span>
    @else
        <span class="badge rounded-pill px-3 py-2 fw-semibold shadow-sm" style="background:#f1f5f9;color:#64748b;font-size:.85rem;">
            ✓ Đã đóng
        </span>
    @endif
</div>

<div class="row g-3 g-md-4">

    {{-- Thông tin phiếu --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom px-4 pt-3 pt-md-4 pb-3">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Thông Tin Phiếu
                </h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-2 g-md-3">
                    <div class="col-12 col-md-4">
                        <div class="text-secondary small fw-semibold">Đơn vị khách</div>
                        <div class="fw-bold fs-6">{{ $ticket->guest_unit }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="text-secondary small fw-semibold">Ngày đến</div>
                        <div class="fw-bold">{{ $ticket->visit_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="text-secondary small fw-semibold">Người tạo phiếu</div>
                        <div class="fw-bold text-truncate">{{ $ticket->creator->name ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-secondary small fw-semibold">Mục đích vào công ty</div>
                        <div class="fw-medium text-dark">{{ $ticket->purpose }}</div>
                    </div>
                    @if($ticket->isClosed())
                    <div class="col-6 col-md-6 border-top pt-2 mt-2">
                        <div class="text-secondary small fw-semibold">Đóng phiếu lúc</div>
                        <div class="small fw-semibold text-muted">{{ $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i') : '—' }}</div>
                    </div>
                    <div class="col-6 col-md-6 border-top pt-2 mt-2">
                        <div class="text-secondary small fw-semibold">Người đóng phiếu</div>
                        <div class="small fw-semibold text-muted">{{ $ticket->closer->name ?? '—' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách khách --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom px-4 pt-3 pt-md-4 pb-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Danh Sách Khách
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1 fw-semibold">{{ $ticket->guests->count() }} người</span>
                </h6>
            </div>
            <div class="card-body p-0 p-md-0">

                @if($canSecurity && $ticket->isOpen())
                {{-- SECURITY FORM --}}
                <form method="POST" action="{{ route('visitor-tickets.security_update', $ticket->id) }}" id="securityForm">
                    @csrf
                @endif

                {{-- ================= GIAO DIỆN DESKTOP / TABLET (TABLE) ================= --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr class="small fw-semibold text-secondary text-uppercase">
                                <th class="px-4 py-3" style="width:50px">STT</th>
                                <th class="py-3" style="min-width:160px">Họ và tên</th>
                                <th class="py-3" style="min-width:120px">Số CCCD</th>
                                <th class="py-3" style="min-width:100px">Thẻ khách</th>
                                <th class="py-3 text-center" style="min-width:140px">Kiểm tra hành lý</th>
                                <th class="py-3" style="min-width:180px">Thời gian vào</th>
                                <th class="py-3" style="min-width:180px">Thời gian ra</th>
                                <th class="py-3 pe-4" style="min-width:160px">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ticket->guests as $i => $guest)
                            <tr class="border-bottom">
                                @if($canSecurity && $ticket->isOpen())
                                <input type="hidden" name="guests[{{ $i }}][id]" value="{{ $guest->id }}">
                                @endif

                                <td class="px-4 text-muted small fw-semibold">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $guest->full_name }}</div>
                                </td>
                                <td class="small text-muted">{{ $guest->id_number ?: '—' }}</td>
                                <td>
                                    @if($guest->guest_card_number)
                                        <span class="badge bg-light border text-dark fw-bold px-2 py-1">{{ $guest->guest_card_number }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Kiểm tra hành lý --}}
                                <td class="text-center">
                                    @if($canSecurity && $ticket->isOpen())
                                        <div class="d-flex justify-content-center gap-3">
                                            <label class="d-flex align-items-center gap-1 small cursor-pointer">
                                                <input type="radio" name="guests[{{ $i }}][baggage_checked]" value="1"
                                                       class="form-check-input mt-0"
                                                       @checked($guest->baggage_checked === true)>
                                                <span class="text-success fw-semibold">Có</span>
                                            </label>
                                            <label class="d-flex align-items-center gap-1 small cursor-pointer">
                                                <input type="radio" name="guests[{{ $i }}][baggage_checked]" value="0"
                                                       class="form-check-input mt-0"
                                                       @checked($guest->baggage_checked === false)>
                                                <span class="text-danger fw-semibold">Không</span>
                                            </label>
                                        </div>
                                    @else
                                        @if(is_null($guest->baggage_checked))
                                            <span class="text-muted small">—</span>
                                        @elseif($guest->baggage_checked)
                                            <span class="badge rounded-pill" style="background:#dcfce7;color:#15803d;">✓ Có</span>
                                        @else
                                            <span class="badge rounded-pill" style="background:#fef2f2;color:#dc2626;">✗ Không</span>
                                        @endif
                                    @endif
                                </td>

                                {{-- Thời gian vào --}}
                                <td>
                                    @if($canSecurity && $ticket->isOpen())
                                        <div class="d-flex gap-1 align-items-center">
                                            <input type="datetime-local" class="form-control form-control-sm"
                                                   id="desk_in_{{ $i }}"
                                                   name="guests[{{ $i }}][checked_in_at]"
                                                   value="{{ $guest->checked_in_at ? $guest->checked_in_at->format('Y-m-d\TH:i') : '' }}">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-now rounded-2" onclick="setNow('desk_in_{{ $i }}', 'mob_in_{{ $i }}')" title="Lấy giờ hiện tại">⏱️</button>
                                        </div>
                                    @else
                                        <span class="{{ $guest->checked_in_at ? 'fw-semibold text-success' : 'text-muted small' }}">
                                            {{ $guest->checked_in_at ? $guest->checked_in_at->format('H:i - d/m/Y') : '—' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Thời gian ra --}}
                                <td>
                                    @if($canSecurity && $ticket->isOpen())
                                        <div class="d-flex gap-1 align-items-center">
                                            <input type="datetime-local" class="form-control form-control-sm"
                                                   id="desk_out_{{ $i }}"
                                                   name="guests[{{ $i }}][checked_out_at]"
                                                   value="{{ $guest->checked_out_at ? $guest->checked_out_at->format('Y-m-d\TH:i') : '' }}">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-now rounded-2" onclick="setNow('desk_out_{{ $i }}', 'mob_out_{{ $i }}')" title="Lấy giờ hiện tại">⏱️</button>
                                        </div>
                                    @else
                                        <span class="{{ $guest->checked_out_at ? 'fw-semibold text-danger' : 'text-muted small' }}">
                                            {{ $guest->checked_out_at ? $guest->checked_out_at->format('H:i - d/m/Y') : '—' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Ghi chú --}}
                                <td class="pe-4">
                                    @if($canSecurity && $ticket->isOpen())
                                        <input type="text" class="form-control form-control-sm"
                                               name="guests[{{ $i }}][note]"
                                               value="{{ $guest->note }}"
                                               placeholder="Ghi chú...">
                                    @else
                                        <span class="{{ $guest->note ? '' : 'text-muted small' }}">
                                            {{ $guest->note ?: '—' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4 small">Chưa có khách nào trong phiếu này.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ================= GIAO DIỆN MOBILE (CARD DÀNH CHO ĐIỆN THOẠI) ================= --}}
                <div class="d-block d-lg-none p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($ticket->guests as $i => $guest)
                        <div class="guest-mobile-card p-3 border">
                            {{-- Card Header --}}
                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark fw-bold rounded-pill">#{{ $i + 1 }}</span>
                                        <span class="fw-bold fs-6 text-dark">{{ $guest->full_name }}</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        CCCD: <span class="fw-semibold text-dark">{{ $guest->id_number ?: '—' }}</span>
                                    </div>
                                </div>
                                <div>
                                    @if($guest->guest_card_number)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 fw-bold">
                                            Thẻ: {{ $guest->guest_card_number }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Chưa có thẻ</span>
                                    @endif
                                </div>
                            </div>

                            @if($canSecurity && $ticket->isOpen())
                            {{-- Form thao tác cho bảo vệ trên điện thoại --}}
                            <div class="row g-2">
                                {{-- Kiểm tra hành lý --}}
                                <div class="col-12 mb-2">
                                    <label class="form-label small fw-bold text-secondary mb-1">Kiểm tra hành lý:</label>
                                    <div class="d-flex gap-3 bg-light p-2 rounded-3 border">
                                        <label class="form-check d-flex align-items-center gap-2 mb-0 cursor-pointer flex-fill">
                                            <input type="radio" name="guests[{{ $i }}][baggage_checked]" value="1"
                                                   class="form-check-input"
                                                   @checked($guest->baggage_checked === true)>
                                            <span class="text-success fw-bold small">✓ Có kiểm tra</span>
                                        </label>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0 cursor-pointer flex-fill">
                                            <input type="radio" name="guests[{{ $i }}][baggage_checked]" value="0"
                                                   class="form-check-input"
                                                   @checked($guest->baggage_checked === false)>
                                            <span class="text-danger fw-bold small">✗ Không kiểm tra</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Giờ vào --}}
                                <div class="col-12 col-sm-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-bold text-success mb-0">Thời gian vào:</label>
                                        <button type="button" class="btn btn-xs btn-outline-success btn-now rounded-pill" onclick="setNow('mob_in_{{ $i }}', 'desk_in_{{ $i }}')">
                                            ⏱️ Vào bây giờ
                                        </button>
                                    </div>
                                    <input type="datetime-local" class="form-control form-control-sm"
                                           id="mob_in_{{ $i }}"
                                           name="guests[{{ $i }}][checked_in_at]"
                                           value="{{ $guest->checked_in_at ? $guest->checked_in_at->format('Y-m-d\TH:i') : '' }}">
                                </div>

                                {{-- Giờ ra --}}
                                <div class="col-12 col-sm-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-bold text-danger mb-0">Thời gian ra:</label>
                                        <button type="button" class="btn btn-xs btn-outline-danger btn-now rounded-pill" onclick="setNow('mob_out_{{ $i }}', 'desk_out_{{ $i }}')">
                                            ⏱️ Ra bây giờ
                                        </button>
                                    </div>
                                    <input type="datetime-local" class="form-control form-control-sm"
                                           id="mob_out_{{ $i }}"
                                           name="guests[{{ $i }}][checked_out_at]"
                                           value="{{ $guest->checked_out_at ? $guest->checked_out_at->format('Y-m-d\TH:i') : '' }}">
                                </div>

                                {{-- Ghi chú --}}
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary mb-1">Ghi chú (nếu có):</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="guests[{{ $i }}][note]"
                                           value="{{ $guest->note }}"
                                           placeholder="Ghi chú mang theo máy tính, thiết bị...">
                                </div>
                            </div>
                            @else
                            {{-- Chế độ xem thông tin trên điện thoại --}}
                            <div class="bg-light p-2 rounded-3 border small">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <span class="text-secondary">Hành lý:</span>
                                        @if(is_null($guest->baggage_checked))
                                            <span class="text-muted">—</span>
                                        @elseif($guest->baggage_checked)
                                            <span class="text-success fw-bold">✓ Có</span>
                                        @else
                                            <span class="text-danger fw-bold">✗ Không</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <span class="text-secondary">Vào:</span>
                                        <span class="{{ $guest->checked_in_at ? 'fw-bold text-success' : 'text-muted' }}">
                                            {{ $guest->checked_in_at ? $guest->checked_in_at->format('H:i d/m') : '—' }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-secondary">Ra:</span>
                                        <span class="{{ $guest->checked_out_at ? 'fw-bold text-danger' : 'text-muted' }}">
                                            {{ $guest->checked_out_at ? $guest->checked_out_at->format('H:i d/m') : '—' }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-secondary">Ghi chú:</span>
                                        <span class="text-dark">{{ $guest->note ?: '—' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">Chưa có thông tin khách.</div>
                        @endforelse
                    </div>
                </div>

                @if($canSecurity && $ticket->isOpen())
                    {{-- Action buttons --}}
                    <div class="px-3 px-md-4 py-3 border-top bg-light d-flex gap-2 flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between">
                        <div class="text-muted small d-none d-md-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Cập nhật thông tin cho từng khách rồi bấm Lưu.
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg py-3 py-md-2 rounded-3 px-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 tap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            LƯU THÔNG TIN BẢO VỆ
                        </button>
                    </div>
                </form>
                @endif

            </div>
        </div>
    </div>

    {{-- Đóng phiếu --}}
    @if($canSecurity && $ticket->isOpen())
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4" style="background:#fff5f5; border: 1px solid #fecaca !important;">
            <div class="card-body p-3 p-md-4 d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="fw-bold text-danger fs-6 mb-1">Đóng Phiếu Đăng Ký</div>
                    <div class="text-muted small">Khi tất cả khách đã ra khỏi công ty, bấm xác nhận đóng phiếu.</div>
                </div>
                <button type="button" class="btn btn-danger btn-lg py-3 py-md-2 rounded-3 px-4 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm tap"
                        data-bs-toggle="modal" data-bs-target="#closeTicketModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    ĐÓNG PHIẾU
                </button>
            </div>
        </div>
    </div>

    {{-- Modal xác nhận đóng phiếu --}}
    <div class="modal fade" id="closeTicketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Xác nhận đóng phiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <p class="text-muted">Bạn có chắc muốn đóng phiếu <strong>#{{ $ticket->id }}</strong> - <strong>{{ $ticket->guest_unit }}</strong>?</p>
                    <div class="alert alert-warning border-0 rounded-3 small py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Thao tác này <strong>không thể hoàn tác</strong>. Phiếu sẽ không thể chỉnh sửa sau khi đóng.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Huỷ</button>
                    <form method="POST" action="{{ route('visitor-tickets.close', $ticket->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-3 fw-bold px-4 py-2">
                            Xác nhận đóng phiếu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    function setNow(id1, id2) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;

        if (id1) {
            const el1 = document.getElementById(id1);
            if (el1) el1.value = formatted;
        }
        if (id2) {
            const el2 = document.getElementById(id2);
            if (el2) el2.value = formatted;
        }
    }
</script>
@endsection
