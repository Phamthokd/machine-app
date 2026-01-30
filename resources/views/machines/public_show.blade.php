@extends('layouts.app-simple')
@section('title','Thông tin thiết bị')

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="mb-3">Thông tin thiết bị</h5>

    <div class="row g-3">
      <div class="col-12">
        <div class="text-muted small">Mã thiết bị</div>
        <div class="fs-5 fw-semibold">{{ $machine->ma_thiet_bi }}</div>
      </div>

      <div class="col-12">
        <div class="text-muted small">Tên thiết bị</div>
        <div class="fw-semibold">{{ $machine->ten_thiet_bi }}</div>
      </div>

      <div class="col-12 col-md-6">
        <div class="text-muted small">Hãng</div>
        <div class="fw-semibold">{{ $machine->brand ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-6">
        <div class="text-muted small">Model</div>
        <div class="fw-semibold">{{ $machine->model ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-6">
        <div class="text-muted small">Serial</div>
        <div class="fw-semibold">{{ $machine->serial ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-6">
        <div class="text-muted small">Số hóa đơn</div>
        <div class="fw-semibold">{{ $machine->invoice_cd ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-4">
        <div class="text-muted small">Năm</div>
        <div class="fw-semibold">{{ $machine->year ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-4">
        <div class="text-muted small">Xuất xứ</div>
        <div class="fw-semibold">{{ $machine->country ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-4">
        <div class="text-muted small">Ngày nhập kho</div>
        <div class="fw-semibold">{{ $machine->stock_in_date ?? '—' }}</div>
      </div>

      <div class="col-12">
        <div class="text-muted small">Vị trí (ghi chú)</div>
        <div class="fw-semibold">{{ $machine->vi_tri_text ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-6">
        <div class="text-muted small">Ngày vào kho</div>
        <div class="fw-semibold">{{ $machine->ngay_vao_kho ?? '—' }}</div>
      </div>

      <div class="col-12 col-md-6">
        <div class="text-muted small">Ngày ra kho</div>
        <div class="fw-semibold">{{ $machine->ngay_ra_kho ?? '—' }}</div>
      </div>

      <div class="col-12">
        <div class="text-muted small">Tổ hiện tại</div>
        <div class="fw-semibold">{{ $machine->department->name ?? '—' }}</div>
      </div>
    </div>

    @role('admin|repair_tech')
      <div class="d-grid gap-2 mt-3">
        <a class="btn btn-primary btn-lg tap"
           href="/repairs/create?machine={{ $machine->ma_thiet_bi }}">
          Tạo phiếu sửa
        </a>
      </div>
    @endrole
  </div>
</div>

@if($machine->repairTickets && $machine->repairTickets->count())
  <div class="card shadow-sm mt-3">
    <div class="card-body">
      <h6 class="mb-3">Lịch sử phiếu sửa (gần nhất)</h6>

      <div class="list-group">
        @foreach($machine->repairTickets as $r)
          <a class="list-group-item list-group-item-action" href="/repairs/{{ $r->id }}">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-semibold">{{ $r->code }}</div>
              <span class="badge text-bg-secondary">{{ $r->status }}</span>
            </div>
            <div class="small text-muted">{{ $r->started_at }} → {{ $r->ended_at }}</div>
          </a>
        @endforeach
      </div>

    </div>
  </div>
@else
  <div class="text-muted small mt-3">Chưa có phiếu sửa.</div>
@endif
@endsection
