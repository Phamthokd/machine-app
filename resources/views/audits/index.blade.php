@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Đánh giá nội bộ')

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="page-title h3 mb-1">📋 Đánh giá nội bộ</h2>
        <div class="text-muted">Quản lý các đợt kiểm tra và đánh giá chất lượng các bộ phận</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-5">
    <div class="col-12">
        <h4 class="h5 mb-3 fw-bold text-dark">Thực hiện đánh giá mới</h4>
        <div class="d-flex flex-wrap gap-3">
            @forelse($templates as $template)
                <div class="card border-0 shadow-sm rounded-3" style="min-width: 300px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded p-2 me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </div>
                            <div>
                                <h5 class="card-title mb-1 fw-bold">{{ $template->name }}</h5>
                                <span class="badge bg-light text-secondary border">Bộ phận: {{ $template->department_name }}</span>
                            </div>
                        </div>
                        <a href="/audits/create?template_id={{ $template->id }}" class="btn btn-primary w-100 fw-medium">
                            Bắt đầu Đánh giá
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-muted w-100 p-4 bg-white rounded-3 shadow-sm text-center">
                    Chưa có bộ câu hỏi (Template) nào được kích hoạt.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h4 class="h5 mb-3 fw-bold text-dark">Lịch sử đánh giá</h4>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary fw-semibold">ID</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">Bộ phiếu</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">Người đánh giá</th>
                            <th class="py-3 px-4 text-secondary fw-semibold">Thời gian</th>
                            <th class="py-3 px-4 text-secondary fw-semibold text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            <tr>
                                <td class="py-3 px-4 text-muted small">#{{ $audit->id }}</td>
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark">{{ $audit->template->name }}</div>
                                    <div class="small text-muted">{{ $audit->template->department_name }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle p-1">👤</div>
                                        <span>{{ $audit->auditor->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-muted small">
                                    {{ $audit->created_at->format('H:i d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="/audits/{{ $audit->id }}" class="btn btn-sm btn-light border text-primary">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-muted">Chưa có lịch sử đánh giá nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($audits->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $audits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
