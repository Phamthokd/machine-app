@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Đánh giá 7S 2.0')

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">

  {{-- Header & Action --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2">
        <h4 class="fw-bold mb-0 text-dark">Kiểm tra & Đánh giá 7S 2.0</h4>
        <span class="badge bg-danger rounded-pill px-2.5 py-1">V2.0 MỚI</span>
      </div>
      <div class="text-muted small mt-1">Đánh giá linh hoạt theo lỗi vi phạm thực tế (Lỗi nhẹ -1đ, Lỗi nặng -2đ)</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('seven-s.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1.5 shadow-sm rounded-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Lịch sử 7S 1.0 cũ</span>
      </a>
      <a href="{{ route('seven-s-v2.create') }}" class="btn btn-primary d-flex align-items-center gap-1.5 shadow-sm rounded-3 fw-semibold px-3.5">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
        <span>Tạo đợt đánh giá 7S 2.0</span>
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Summary Stats Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
        <div class="text-secondary small fw-medium mb-1">Tổng đợt kiểm tra</div>
        <div class="fs-3 fw-bold text-dark">{{ number_format($stats['total_inspections']) }}</div>
        <div class="small text-muted mt-auto pt-1">Đợt kiểm tra 7S 2.0</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-warning">
        <div class="text-secondary small fw-medium mb-1">Lỗi nhẹ phát hiện</div>
        <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total_light']) }}</div>
        <div class="small text-muted mt-auto pt-1">-1 điểm / lỗi</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-danger">
        <div class="text-secondary small fw-medium mb-1">Lỗi nặng phát hiện</div>
        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['total_heavy']) }}</div>
        <div class="small text-muted mt-auto pt-1">-2 điểm / lỗi</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-primary">
        <div class="text-secondary small fw-medium mb-1">Chờ khắc phục</div>
        <div class="fs-3 fw-bold {{ $stats['total_pending'] > 0 ? 'text-danger' : 'text-success' }}">
          {{ number_format($stats['total_pending']) }}
        </div>
        <div class="small text-muted mt-auto pt-1">Lỗi chưa nghiệm thu</div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
      <form action="{{ route('seven-s-v2.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label small text-secondary fw-semibold mb-1">Bộ phận</label>
          <select name="department" class="form-select form-select-sm rounded-3">
            <option value="all">-- Tất cả bộ phận --</option>
            @foreach($departments as $dept)
              <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small text-secondary fw-semibold mb-1">Từ ngày</label>
          <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm rounded-3">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small text-secondary fw-semibold mb-1">Đến ngày</label>
          <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm rounded-3">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small text-secondary fw-semibold mb-1">Tình trạng lỗi</label>
          <select name="filter_status" class="form-select form-select-sm rounded-3">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="has_faults" {{ request('filter_status') == 'has_faults' ? 'selected' : '' }}>Có bắt lỗi (Điểm trừ)</option>
            <option value="clean" {{ request('filter_status') == 'clean' ? 'selected' : '' }}>Không có lỗi (Đạt 0 điểm trừ)</option>
            <option value="pending_remedy" {{ request('filter_status') == 'pending_remedy' ? 'selected' : '' }}>Còn lỗi chưa khắc phục</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold">Lọc dữ liệu</button>
          <a href="{{ route('seven-s-v2.index') }}" class="btn btn-light btn-sm rounded-3 border" title="Đặt lại">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Record List Table --}}
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-secondary small text-uppercase fw-semibold border-bottom">
          <tr>
            <th class="ps-3 py-3">Mã phiếu / Ngày</th>
            <th>Bộ phận</th>
            <th>Người đánh giá</th>
            <th>Chi tiết lỗi</th>
            <th class="text-center">Tổng điểm trừ</th>
            <th>Trạng thái khắc phục</th>
            <th class="text-end pe-3">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($records as $rec)
            <tr>
              <td class="ps-3">
                <a href="{{ route('seven-s-v2.show', $rec->id) }}" class="fw-bold text-primary text-decoration-none">
                  #7S2-{{ str_pad($rec->id, 4, '0', STR_PAD_LEFT) }}
                </a>
                <div class="text-muted small">{{ $rec->created_at->format('d/m/Y H:i') }}</div>
              </td>
              <td>
                <span class="badge bg-secondary bg-opacity-10 text-dark fw-bold px-2.5 py-1.5 rounded-3">
                  {{ $rec->department }}
                </span>
              </td>
              <td>
                <div class="fw-medium text-dark">{{ $rec->inspector->name ?? 'N/A' }}</div>
                <div class="text-muted small">{{ $rec->inspector->email ?? '' }}</div>
              </td>
              <td>
                @if($rec->light_fault_count == 0 && $rec->heavy_fault_count == 0)
                  <span class="text-success small fw-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-shield-check me-1 inline-block" viewBox="0 0 16 16"><path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/><path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/></svg>
                    Đạt chuẩn (0 lỗi)
                  </span>
                @else
                  <div class="d-flex gap-1.5 flex-wrap">
                    @if($rec->light_fault_count > 0)
                      <span class="badge bg-warning text-dark border border-warning px-2.5 py-1 fw-bold shadow-sm">
                        {{ $rec->light_fault_count }} lỗi nhẹ
                      </span>
                    @endif
                    @if($rec->heavy_fault_count > 0)
                      <span class="badge bg-danger text-white border border-danger px-2.5 py-1 fw-bold shadow-sm">
                        {{ $rec->heavy_fault_count }} lỗi nặng
                      </span>
                    @endif
                  </div>
                @endif
              </td>
              <td class="text-center">
                @if($rec->total_penalty_points < 0)
                  <span class="badge bg-danger fs-6 px-3 py-1.5 rounded-pill shadow-sm">
                    {{ $rec->total_penalty_points }} điểm
                  </span>
                @else
                  <span class="badge bg-success fs-6 px-3 py-1.5 rounded-pill shadow-sm">
                    0 điểm
                  </span>
                @endif
              </td>
              <td>
                @if($rec->issues->isEmpty())
                  <span class="badge bg-success bg-opacity-10 text-success">Không cần khắc phục</span>
                @elseif($rec->isAllVerified())
                  <span class="badge bg-success text-white">Đã nghiệm thu xong</span>
                @elseif($rec->improved_issues_count > 0)
                  <span class="badge bg-info text-white">Có {{ $rec->improved_issues_count }} chờ duyệt</span>
                @else
                  <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                    {{ $rec->pending_issues_count }} lỗi chưa sửa
                  </span>
                @endif
              </td>
              <td class="text-end pe-3">
                <a href="{{ route('seven-s-v2.show', $rec->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2.5">
                  Chi tiết
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <div class="mb-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" class="text-secondary opacity-50" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="fw-semibold">Chưa có đợt đánh giá 7S 2.0 nào</div>
                <div class="small mt-1">Bấm "Tạo đợt đánh giá 7S 2.0" để bắt đầu ghi nhận đợt kiểm tra đầu tiên.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($records->hasPages())
      <div class="p-3 border-top bg-light d-flex justify-content-end">
        {{ $records->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
