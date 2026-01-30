@extends('layouts.app-simple')
@section('title','Danh sách phiếu sửa')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
  <div>
    <h4 class="mb-1">Danh sách phiếu sửa</h4>
    <div class="text-muted small">Tổng {{ $repairs->total() }} phiếu gần nhất.</div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary btn-sm tap" href="/repairs/export">Xuất Excel</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Mã phiếu</th>
          <th>Mã thiết bị</th>
          <th>Tên thiết bị</th>
          <th>Tổ</th>
          <th>Bắt đầu</th>
          <th>Kết thúc</th>
          <th>Trạng thái</th>
        </tr>
      </thead>
      <tbody>
        @foreach($repairs as $r)
          <tr>
            <td><a class="fw-semibold text-decoration-none" href="/repairs/{{ $r->id }}">{{ $r->code }}</a></td>
            <td><a class="text-decoration-none" href="/m/{{ $r->machine->ma_thiet_bi }}">{{ $r->machine->ma_thiet_bi }}</a></td>
            <td>{{ $r->machine->ten_thiet_bi }}</td>
            <td>{{ $r->machine->department->name ?? '—' }}</td>
            <td>{{ $r->started_at }}</td>
            <td>{{ $r->ended_at }}</td>
            <td>
              <span class="badge text-bg-secondary">{{ $r->status }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $repairs->links() }}
</div>
@endsection
