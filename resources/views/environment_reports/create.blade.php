@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Tạo báo cáo môi trường')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="text-uppercase text-primary fw-bold small mb-2">New Report</div>
            <h1 class="h3 fw-bold mb-1">Tạo báo cáo nhiệt độ và độ ẩm</h1>
            <div class="text-muted">Nhập số liệu theo từng ngày, giữ cấu trúc tương đương biểu mẫu giấy hiện tại.</div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('environment-reports.store') }}">
    @csrf
    @include('environment_reports._form')
</form>
@endsection
