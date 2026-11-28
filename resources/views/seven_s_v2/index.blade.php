@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.7s_v2_title'))

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">

  {{-- Header & Action --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2">
        <h4 class="fw-bold mb-0 text-dark">{{ __('messages.7s_v2_title') }}</h4>
        <span class="badge bg-danger rounded-pill px-2.5 py-1">{{ __('messages.7s_v2_badge_new') }}</span>
      </div>
      <div class="text-muted small mt-1">{{ __('messages.7s_v2_subtitle') }}</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('seven-s.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1.5 shadow-sm rounded-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ __('messages.7s_v2_history_v1') }}</span>
      </a>
      <a href="{{ route('seven-s-v2.create') }}" class="btn btn-primary d-flex align-items-center gap-1.5 shadow-sm rounded-3 fw-semibold px-3.5">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
        <span>{{ __('messages.7s_v2_create_btn') }}</span>
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
        <div class="text-secondary small fw-medium mb-1">{{ __('messages.7s_v2_stat_total_inspections') }}</div>
        <div class="fs-3 fw-bold text-dark">{{ number_format($stats['total_inspections']) }}</div>
        <div class="small text-muted mt-auto pt-1">{{ __('messages.7s_v2_stat_inspections_desc') }}</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-warning">
        <div class="text-secondary small fw-medium mb-1">{{ __('messages.7s_v2_stat_light_detected') }}</div>
        <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total_light']) }}</div>
        <div class="small text-muted mt-auto pt-1">{{ __('messages.7s_v2_stat_light_desc') }}</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-danger">
        <div class="text-secondary small fw-medium mb-1">{{ __('messages.7s_v2_stat_heavy_detected') }}</div>
        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['total_heavy']) }}</div>
        <div class="small text-muted mt-auto pt-1">{{ __('messages.7s_v2_stat_heavy_desc') }}</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-start border-4 border-primary">
        <div class="text-secondary small fw-medium mb-1">{{ __('messages.7s_v2_stat_pending_remedy') }}</div>
        <div class="fs-3 fw-bold {{ $stats['total_pending'] > 0 ? 'text-danger' : 'text-success' }}">
          {{ number_format($stats['total_pending']) }}
        </div>
        <div class="small text-muted mt-auto pt-1">{{ __('messages.7s_v2_stat_pending_desc') }}</div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
      <form action="{{ route('seven-s-v2.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.7s_v2_filter_department') }}</label>
          <select name="department" class="form-select form-select-sm rounded-3">
            <option value="all">{{ __('messages.7s_v2_filter_all_depts') }}</option>
            @foreach($departments as $dept)
              <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                {{ \Illuminate\Support\Facades\Lang::has('messages.' . $dept) ? __('messages.' . $dept) : $dept }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.7s_v2_filter_from_date') }}</label>
          <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm rounded-3">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.7s_v2_filter_to_date') }}</label>
          <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm rounded-3">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small text-secondary fw-semibold mb-1">{{ __('messages.7s_v2_filter_status_label') }}</label>
          <select name="filter_status" class="form-select form-select-sm rounded-3">
            <option value="">{{ __('messages.7s_v2_filter_all_statuses') }}</option>
            <option value="has_faults" {{ request('filter_status') == 'has_faults' ? 'selected' : '' }}>{{ __('messages.7s_v2_filter_has_faults') }}</option>
            <option value="clean" {{ request('filter_status') == 'clean' ? 'selected' : '' }}>{{ __('messages.7s_v2_filter_clean') }}</option>
            <option value="pending_remedy" {{ request('filter_status') == 'pending_remedy' ? 'selected' : '' }}>{{ __('messages.7s_v2_filter_pending_remedy') }}</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold">{{ __('messages.7s_v2_btn_filter') }}</button>
          <a href="{{ route('seven-s-v2.index') }}" class="btn btn-light btn-sm rounded-3 border" title="{{ __('messages.7s_v2_btn_reset') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Chart / Report Section --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-3.5 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
      <div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M18 20V10m-6 10V4m-6 16v-6"/></svg>
          </span>
          <h5 class="fw-bold mb-0 text-dark">{{ __('messages.7s_v2_chart_title') }}</h5>
        </div>
        <div class="text-muted small mt-1">
          {{ __('messages.7s_v2_chart_subtitle') }}
          @if(request('start_date') || request('end_date'))
            <span class="badge bg-light text-secondary border ms-1">
              {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '...' }}
              →
              {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '...' }}
            </span>
          @endif
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div class="btn-group btn-group-sm" role="group" id="chartTypeToggle">
          <button type="button" class="btn btn-outline-primary active fw-medium px-3" data-mode="penalty">
            {{ __('messages.7s_v2_chart_view_penalty') }}
          </button>
          <button type="button" class="btn btn-outline-primary fw-medium px-3" data-mode="faults">
            {{ __('messages.7s_v2_chart_view_faults') }}
          </button>
        </div>
        <button class="btn btn-light btn-sm border rounded-3 text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#chartCollapse" aria-expanded="true" aria-controls="chartCollapse" title="{{ __('messages.7s_v2_chart_toggle') }}">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
        </button>
      </div>
    </div>

    <div class="collapse show" id="chartCollapse">
      <div class="card-body pt-1 pb-3 px-3 px-md-4">
        @if(count($chartData) > 0)
          <div style="position: relative; height: 340px; width: 100%;">
            <canvas id="sevenSBarChart"></canvas>
          </div>
          <div class="row g-2 mt-3 pt-2 border-top">
            <div class="col-12 col-md-4 text-center text-md-start">
              <span class="small text-muted">{{ __('messages.7s_v2_chart_stat_total_depts') }}:</span>
              <strong class="text-dark ms-1">{{ count($chartData) }}</strong>
            </div>
            <div class="col-12 col-md-4 text-center">
              <span class="small text-muted">{{ __('messages.7s_v2_chart_stat_max_penalty') }}:</span>
              <strong class="text-danger ms-1">
                {{ $chartData->first()['department'] ?? '' }} (-{{ $chartData->first()['total_penalty'] ?? 0 }}đ)
              </strong>
            </div>
            <div class="col-12 col-md-4 text-center text-md-end">
              <span class="small text-muted">{{ __('messages.7s_v2_chart_stat_clean_depts') }}:</span>
              <strong class="text-success ms-1">
                {{ $chartData->where('total_penalty', 0)->count() }}
              </strong>
            </div>
          </div>
        @else
          <div class="text-center py-4 text-muted">
            <div class="small">{{ __('messages.7s_v2_chart_no_data') }}</div>
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Record List Table --}}
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-secondary small text-uppercase fw-semibold border-bottom">
          <tr>
            <th class="ps-3 py-3">{{ __('messages.7s_v2_col_code_date') }}</th>
            <th>{{ __('messages.7s_v2_col_dept') }}</th>
            <th>{{ __('messages.7s_v2_col_inspector') }}</th>
            <th>{{ __('messages.7s_v2_col_fault_details') }}</th>
            <th class="text-center">{{ __('messages.7s_v2_col_total_penalty') }}</th>
            <th>{{ __('messages.7s_v2_col_remediation_status') }}</th>
            <th class="text-end pe-3">{{ __('messages.7s_v2_col_actions') }}</th>
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
                  {{ \Illuminate\Support\Facades\Lang::has('messages.' . $rec->department) ? __('messages.' . $rec->department) : $rec->department }}
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
                    {{ __('messages.7s_v2_zero_faults') }}
                  </span>
                @else
                  <div class="d-flex gap-1.5 flex-wrap">
                    @if($rec->light_fault_count > 0)
                      <span class="badge bg-warning text-dark border border-warning px-2.5 py-1 fw-bold shadow-sm">
                        {{ __('messages.7s_v2_light_faults_count', ['count' => $rec->light_fault_count]) }}
                      </span>
                    @endif
                    @if($rec->heavy_fault_count > 0)
                      <span class="badge bg-danger text-white border border-danger px-2.5 py-1 fw-bold shadow-sm">
                        {{ __('messages.7s_v2_heavy_faults_count', ['count' => $rec->heavy_fault_count]) }}
                      </span>
                    @endif
                  </div>
                @endif
              </td>
              <td class="text-center">
                @if($rec->total_penalty_points < 0)
                  <span class="badge bg-danger fs-6 px-3 py-1.5 rounded-pill shadow-sm">
                    {{ __('messages.7s_v2_points_count', ['points' => $rec->total_penalty_points]) }}
                  </span>
                @else
                  <span class="badge bg-success fs-6 px-3 py-1.5 rounded-pill shadow-sm">
                    {{ __('messages.7s_v2_points_count', ['points' => 0]) }}
                  </span>
                @endif
              </td>
              <td>
                @if($rec->issues->isEmpty())
                  <span class="badge bg-success bg-opacity-10 text-success">{{ __('messages.7s_v2_remedy_not_needed') }}</span>
                @elseif($rec->isAllVerified())
                  <span class="badge bg-success text-white">{{ __('messages.7s_v2_remedy_completed') }}</span>
                @elseif($rec->improved_issues_count > 0)
                  <span class="badge bg-info text-white">{{ __('messages.7s_v2_remedy_waiting_approval', ['count' => $rec->improved_issues_count]) }}</span>
                @else
                  <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                    {{ __('messages.7s_v2_remedy_pending_count', ['count' => $rec->pending_issues_count]) }}
                  </span>
                @endif
              </td>
              <td class="text-end pe-3">
                <div class="d-inline-flex align-items-center gap-1.5">
                  <a href="{{ route('seven-s-v2.show', $rec->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2.5">
                    {{ __('messages.7s_v2_btn_detail') }}
                  </a>
                  @if(auth()->user() && auth()->user()->isAdminUser())
                    <form action="{{ route('seven-s-v2.destroy', $rec->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('{{ __('messages.7s_v2_delete_confirm') }}')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 31px; height: 31px;" title="{{ __('messages.7s_v2_btn_delete') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <div class="mb-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" class="text-secondary opacity-50" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="fw-semibold">{{ __('messages.7s_v2_empty_records') }}</div>
                <div class="small mt-1">{{ __('messages.7s_v2_empty_records_desc') }}</div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const chartCanvas = document.getElementById('sevenSBarChart');
  if (!chartCanvas) return;

  const rawData = @json($chartData ?? []);
  if (!rawData || rawData.length === 0) return;

  const labels = rawData.map(d => d.department);
  const penalties = rawData.map(d => d.total_penalty);
  const lightFaults = rawData.map(d => d.light_faults);
  const heavyFaults = rawData.map(d => d.heavy_faults);
  const inspections = rawData.map(d => d.inspections);

  let currentMode = 'penalty';

  // Config data for Penalty Mode
  const penaltyDataConfig = {
    labels: labels,
    datasets: [{
      label: '{{ __("messages.7s_v2_chart_dataset_penalty") }} (-đ)',
      data: penalties,
      backgroundColor: 'rgba(239, 68, 68, 0.85)',
      borderColor: 'rgb(220, 38, 38)',
      borderWidth: 1.5,
      borderRadius: 6,
      barPercentage: 0.6,
      categoryPercentage: 0.7,
    }]
  };

  // Config data for Faults Breakdown Mode
  const faultsDataConfig = {
    labels: labels,
    datasets: [
      {
        label: '{{ __("messages.7s_v2_chart_dataset_light") }} (-1đ)',
        data: lightFaults,
        backgroundColor: 'rgba(245, 158, 11, 0.85)',
        borderColor: 'rgb(217, 119, 6)',
        borderWidth: 1.5,
        borderRadius: 6,
      },
      {
        label: '{{ __("messages.7s_v2_chart_dataset_heavy") }} (-2đ)',
        data: heavyFaults,
        backgroundColor: 'rgba(220, 38, 38, 0.85)',
        borderColor: 'rgb(185, 28, 28)',
        borderWidth: 1.5,
        borderRadius: 6,
      }
    ]
  };

  const chart = new Chart(chartCanvas, {
    type: 'bar',
    data: penaltyDataConfig,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            boxWidth: 14,
            usePointStyle: true,
            pointStyle: 'rectRounded',
            font: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '500'
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.9)',
          padding: 12,
          cornerRadius: 8,
          titleFont: { size: 13, weight: '700' },
          bodyFont: { size: 12 },
          callbacks: {
            label: function(context) {
              const idx = context.dataIndex;
              const val = context.raw || 0;
              if (currentMode === 'penalty') {
                return ` ${context.dataset.label}: -${val}đ (${inspections[idx]} đợt kiểm tra)`;
              } else {
                return ` ${context.dataset.label}: ${val} lỗi`;
              }
            },
            afterBody: function(contexts) {
              if (contexts.length > 0 && currentMode === 'penalty') {
                const idx = contexts[0].dataIndex;
                return [
                  `• Lỗi nhẹ: ${lightFaults[idx]} lỗi`,
                  `• Lỗi nặng: ${heavyFaults[idx]} lỗi`
                ];
              }
              return [];
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            font: { family: "'Inter', sans-serif", size: 11, weight: '500' },
            color: '#475569',
            maxRotation: 30,
            minRotation: 0,
          }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(226, 232, 240, 0.6)' },
          ticks: {
            precision: 0,
            font: { family: "'Inter', sans-serif", size: 11 },
            color: '#64748b',
            callback: function(value) {
              return currentMode === 'penalty' ? (value > 0 ? '-' + value : '0') : value;
            }
          }
        }
      }
    }
  });

  // Toggle between Penalty Points vs Faults Breakdown
  const toggleButtons = document.querySelectorAll('#chartTypeToggle button');
  toggleButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      toggleButtons.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      const mode = this.getAttribute('data-mode');
      currentMode = mode;

      if (mode === 'faults') {
        chart.data = faultsDataConfig;
        chart.options.scales.x.stacked = true;
        chart.options.scales.y.stacked = true;
      } else {
        chart.data = penaltyDataConfig;
        chart.options.scales.x.stacked = false;
        chart.options.scales.y.stacked = false;
      }
      chart.update();
    });
  });
});
</script>
@endpush
@endsection
