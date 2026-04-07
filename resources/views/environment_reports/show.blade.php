@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Chi tiết báo cáo môi trường')

@section('content')
<style>
    .summary-card,
    .grid-card {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(148, 163, 184, 0.15);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .report-view-table {
        min-width: 1700px;
        font-size: 0.9rem;
    }

    .report-view-table th {
        background: #f8fafc;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<div class="d-grid gap-4">
    <div class="summary-card p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-primary fw-bold small mb-2">Environment Report</div>
                <h1 class="h3 fw-bold mb-2">{{ $report->department_name }} · {{ $report->period_label }}</h1>
                <div class="text-muted">Người tạo: {{ $report->creator->name ?? 'N/A' }} · Tạo lúc {{ $report->created_at->format('H:i d/m/Y') }}</div>
                @if($report->note)
                    <div class="mt-3 p-3 bg-light rounded-4 text-muted">{{ $report->note }}</div>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge {{ $report->status === 'submitted' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} fs-6 px-3 py-2">
                    {{ $report->status === 'submitted' ? 'Đã chốt' : 'Nháp' }}
                </span>
                <a href="{{ route('environment-reports.edit', $report) }}" class="btn btn-outline-primary">Sửa</a>
                <a href="{{ route('environment-reports.print', $report) }}" target="_blank" class="btn btn-primary">In / Xuất</a>
            </div>
        </div>
    </div>

    <div class="grid-card">
        <div class="table-responsive">
            <table class="table table-bordered report-view-table mb-0">
                <thead>
                    <tr>
                        <th rowspan="2">Ngày</th>
                        <th colspan="4">Độ ẩm (%)</th>
                        <th colspan="4">Nhiệt độ (°C)</th>
                        <th rowspan="2">Thời tiết</th>
                        <th colspan="4">Cải thiện</th>
                        <th rowspan="2">Người kiểm tra</th>
                    </tr>
                    <tr>
                        @foreach($timeSlots as $slot)
                            <th>{{ substr($slot, 0, 2) . ':' . substr($slot, 2, 2) }}</th>
                        @endforeach
                        @foreach($timeSlots as $slot)
                            <th>{{ substr($slot, 0, 2) . ':' . substr($slot, 2, 2) }}</th>
                        @endforeach
                        @foreach($timeSlots as $slot)
                            <th>{{ substr($slot, 0, 2) . ':' . substr($slot, 2, 2) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->entries as $entry)
                        <tr>
                            <td class="fw-bold text-center">{{ $entry->day_number }}</td>
                            @foreach($timeSlots as $slot)
                                <td>{{ $entry->{'humidity_' . $slot} }}</td>
                            @endforeach
                            @foreach($timeSlots as $slot)
                                <td>{{ $entry->{'temperature_' . $slot} }}</td>
                            @endforeach
                            <td>{{ $entry->weather }}</td>
                            @foreach($timeSlots as $slot)
                                <td>{{ $entry->{'action_' . $slot} }}</td>
                            @endforeach
                            <td>{{ $entry->checked_by }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
