@extends('layouts.app-simple')
@section('title','Danh sách phiếu sửa')

@section('content')
<h2>Danh sách phiếu sửa</h2>

<table>
  <thead>
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
        <td><a href="/repairs/{{ $r->id }}">{{ $r->code }}</a></td>
        <td><a href="/m/{{ $r->machine->ma_thiet_bi }}">{{ $r->machine->ma_thiet_bi }}</a></td>
        <td>{{ $r->machine->ten_thiet_bi }}</td>
        <td>{{ $r->machine->department->name ?? '' }}</td>
        <td>{{ $r->started_at }}</td>
        <td>{{ $r->ended_at }}</td>
        <td>{{ $r->status }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

{{-- Pagination: nếu bạn dùng paginate() --}}
<div style="margin-top:12px;">
  {{ $repairs->links() }}
</div>
@endsection
