@extends('layouts.app-simple')
@section('title','Danh sách phiếu sửa')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
  <div>
    <h4 class="mb-1">Danh sách phiếu sửa</h4>
    
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
          
          <th>Mã thiết bị</th>
          <th>Tên thiết bị</th>
          <th>Mã hàng</th>
          <th>Công đoạn</th>
          <th>Nguyên nhân</th>
          <th>Nội dung sửa chữa</th>
          <th>Tổ</th>
          <th>Bắt đầu</th>
          <th>Kết thúc</th>
          <th>Tạo bởi</th>
          
        </tr>
      </thead>
      <tbody>
        @foreach($repairs as $r)
          <tr>
            
            <td><a class="text-decoration-none" href="/m/{{ $r->machine->ma_thiet_bi }}">{{ $r->machine->ma_thiet_bi }}</a></td>
            <td>{{ $r->machine->ten_thiet_bi }}</td>
            <td>{{ $r->ma_hang }}</td>
            <td>{{ $r->cong_doan }}</td>
            <td>{{ $r->nguyen_nhan }}</td>
            <td>{{ $r->noi_dung_sua_chua }}</td>
            <td>{{ $r->machine->department->name ?? '—' }}</td>
            <td>{{ $r->started_at }}</td>
            <td>{{ $r->ended_at }}</td>
            <td>{{ $r->createdBy->name ?? '—' }}</td>
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
