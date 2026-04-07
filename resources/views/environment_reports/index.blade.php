@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Báo cáo môi trường')

@section('content')
<style>
    .hero-card,
    .list-card {
        background: white;
        border-radius: 22px;
        border: 1px solid rgba(148, 163, 184, 0.15);
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    }

    .report-table thead th {
        background: #f8fafc;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
</style>

<div class="d-grid gap-4">
    <div class="hero-card p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="text-uppercase text-primary fw-bold small mb-2">Humidity / Temperature</div>
                <h1 class="h3 fw-bold mb-2">Báo cáo môi trường nhà máy</h1>
                <div class="text-muted">Quản lý phiếu nhiệt độ, độ ẩm, thời tiết và hành động cải thiện theo tháng trên hệ thống.</div>
            </div>
            <a href="{{ route('environment-reports.create') }}" class="btn btn-primary btn-lg px-4">Tạo báo cáo mới</a>
        </div>
    </div>

    <div class="list-card p-4">
        <form method="GET" action="{{ route('environment-reports.index') }}" class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Bộ phận</label>
                <select name="department_name" class="form-select">
                    <option value="all">Tất cả</option>
                    @foreach($departments as $department)
                        <option value="{{ $department }}" @selected(request('department_name') === $department)>{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Năm</label>
                <select name="report_year" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected((string) request('report_year') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Tháng</label>
                <select name="report_month" class="form-select">
                    <option value="">Tất cả</option>
                    @for($month = 1; $month <= 12; $month++)
                        <option value="{{ $month }}" @selected((string) request('report_month') === (string) $month)>Tháng {{ $month }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Lọc</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle report-table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bộ phận</th>
                        <th>Kỳ báo cáo</th>
                        <th>Người tạo</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td class="fw-semibold">{{ $report->department_name }}</td>
                            <td>{{ $report->period_label }}</td>
                            <td>{{ $report->creator->name ?? 'N/A' }}</td>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $report->status === 'submitted' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    {{ $report->status === 'submitted' ? 'Đã chốt' : 'Nháp' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('environment-reports.show', $report) }}" class="btn btn-sm btn-light border">Xem</a>
                                    <a href="{{ route('environment-reports.edit', $report) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                    <a href="{{ route('environment-reports.print', $report) }}" class="btn btn-sm btn-outline-secondary" target="_blank">In</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Chưa có báo cáo môi trường nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="pt-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
