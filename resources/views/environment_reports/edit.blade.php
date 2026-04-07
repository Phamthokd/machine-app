@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Sửa báo cáo môi trường')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="text-uppercase text-primary fw-bold small mb-2">Edit Report</div>
            <h1 class="h3 fw-bold mb-1">Cập nhật báo cáo {{ $report->period_label }}</h1>
            <div class="text-muted">{{ $report->department_name }} · Người tạo: {{ $report->creator->name ?? 'N/A' }}</div>
        </div>
        <a href="{{ route('environment-reports.show', $report) }}" class="btn btn-outline-secondary">Xem chi tiết</a>
    </div>
</div>

<form method="POST" action="{{ route('environment-reports.update', $report) }}">
    @csrf
    @method('PUT')
    @include('environment_reports._form')
</form>
@endsection
