@extends('layouts.app-simple')
@section('title','Chi tiết phiếu sửa')

@section('content')
<p>
  <a href="/repairs">← Danh sách phiếu sửa</a> |
  <a href="/m/{{ $repair->machine->ma_thiet_bi }}">Trang máy</a>
</p>

<div style="border:1px solid #ddd;padding:16px;max-width:900px">
  <h2>Phiếu sửa: {{ $repair->code }}</h2>

  <p><strong>Thiết bị:</strong> {{ $repair->machine->ma_thiet_bi }} - {{ $repair->machine->ten_thiet_bi }}</p>
  <p><strong>Tổ:</strong> {{ $repair->machine->department->name ?? '' }}</p>

  <p><strong>Mã hàng:</strong> {{ $repair->ma_hang }}</p>
  <p><strong>Công đoạn:</strong> {{ $repair->cong_doan }}</p>

  <p><strong>Nguyên nhân:</strong><br>{{ $repair->nguyen_nhan }}</p>
  <p><strong>Nội dung sửa chữa:</strong><br>{{ $repair->noi_dung_sua_chua }}</p>

  <p><strong>Bắt đầu:</strong> {{ $repair->started_at }}</p>
  <p><strong>Kết thúc:</strong> {{ $repair->ended_at }}</p>

  <p><strong>Trạng thái:</strong> {{ $repair->status }}</p>
  <p><strong>Tạo bởi:</strong> {{ $repair->createdBy->name ?? '' }}</p>
</div>
@endsection
