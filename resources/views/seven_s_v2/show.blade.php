@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.7s_v2_record_title') . $record->id)

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">

  {{-- Header & Navigation --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('seven-s-v2.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
      </a>
      <div>
        <div class="d-flex align-items-center gap-2">
          <h4 class="fw-bold mb-0 text-dark">{{ __('messages.7s_v2_record_title') }}{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}</h4>
          <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1">
            {{ \Illuminate\Support\Facades\Lang::has('messages.' . $record->department) ? __('messages.' . $record->department) : $record->department }}
          </span>
        </div>
        <div class="text-muted small mt-1">
          {{ __('messages.7s_v2_check_time', ['time' => $record->created_at->format('d/m/Y H:i'), 'name' => $record->inspector->name ?? 'N/A']) }}
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <a href="javascript:window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-flex align-items-center gap-1 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12 0v4h12v-4"/></svg>
        <span>{{ __('messages.7s_v2_btn_print') }}</span>
      </a>

      @if(auth()->user() && auth()->user()->isAdminUser())
        <form action="{{ route('seven-s-v2.destroy', $record->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.7s_v2_delete_confirm') }}')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            <span>{{ __('messages.7s_v2_btn_delete') }}</span>
          </button>
        </form>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Result Banner --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    @if($record->total_penalty_points < 0)
      <div class="bg-danger text-white p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <div class="text-white text-opacity-75 text-uppercase small fw-bold">{{ __('messages.7s_v2_result_header') }}</div>
            <div class="display-5 fw-bold my-1">{{ __('messages.7s_v2_points_count', ['points' => $record->total_penalty_points]) }}</div>
            <div class="text-white text-opacity-90">
              {!! __('messages.7s_v2_detected_summary', ['light' => $record->light_fault_count, 'heavy' => $record->heavy_fault_count]) !!}
            </div>
          </div>
          <div class="text-end">
            <span class="badge bg-white text-danger fs-6 px-3 py-2 rounded-pill shadow-sm">
              {{ __('messages.7s_v2_verified_remedy_counter', ['verified' => $record->issues->where('status', 'verified')->count(), 'total' => $record->issues->count()]) }}
            </span>
          </div>
        </div>
      </div>
    @else
      <div class="bg-success text-white p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <div class="text-white text-opacity-75 text-uppercase small fw-bold">{{ __('messages.7s_v2_result_header') }}</div>
            <div class="display-6 fw-bold my-1">{{ __('messages.7s_v2_result_passed_banner') }}</div>
            <div class="text-white text-opacity-90">{{ __('messages.7s_v2_result_passed_desc') }}</div>
          </div>
          <div>
            <span class="badge bg-white text-success fs-6 px-3 py-2 rounded-pill shadow-sm">{{ __('messages.7s_v2_100_percent_passed') }}</span>
          </div>
        </div>
      </div>
    @endif

    @if($record->general_note)
      <div class="card-body bg-light p-3 border-top">
        <div class="small fw-bold text-secondary mb-1">{{ __('messages.7s_v2_general_note_view') }}</div>
        <div class="text-dark">{{ $record->general_note }}</div>
      </div>
    @endif
  </div>

  {{-- Issues List --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0 text-dark">
      {{ __('messages.7s_v2_issue_list_title', ['count' => $record->issues->count()]) }}
    </h5>
  </div>

  @if($record->issues->isEmpty())
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white mb-4">
      <div class="fs-1 mb-2">🎉</div>
      <h6 class="fw-bold text-success">{{ __('messages.7s_v2_perfect_celebration') }}</h6>
      <p class="text-muted small mb-0">
        {!! __('messages.7s_v2_perfect_celebration_desc', ['dept' => \Illuminate\Support\Facades\Lang::has('messages.' . $record->department) ? __('messages.' . $record->department) : $record->department]) !!}
      </p>
    </div>
  @else
    <div class="d-flex flex-column gap-3 mb-5">
      @foreach($record->issues as $index => $issue)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="card-header bg-white p-3.5 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-dark rounded-pill px-2.5 py-1">#{{ $index + 1 }}</span>
              @if($issue->severity === 'heavy')
                <span class="badge bg-danger text-white fs-7 px-2.5 py-1.5 rounded-pill shadow-sm">{{ __('messages.7s_v2_severity_heavy_title') }}</span>
              @else
                <span class="badge bg-warning text-dark fs-7 px-2.5 py-1.5 rounded-pill shadow-sm">{{ __('messages.7s_v2_severity_light_title') }}</span>
              @endif

              @if($issue->location_detail)
                <span class="text-secondary small ms-2">
                  {!! __('messages.7s_v2_location_tag', ['loc' => $issue->location_detail]) !!}
                </span>
              @endif
            </div>

            @php $badge = $issue->status_badge; @endphp
            <span class="badge {{ $badge['class'] }} px-3 py-1.5 rounded-pill">
              {{ $badge['text'] }}
            </span>
          </div>

          <div class="card-body p-3.5">
            {{-- Content of Issue --}}
            <div class="mb-3">
              <div class="small fw-bold text-secondary mb-1">{{ __('messages.7s_v2_issue_content_label') }}:</div>
              <div class="fs-6 text-dark fw-medium bg-light p-3 rounded-3 border">
                {{ $issue->content }}
              </div>
            </div>

            {{-- Inspection Photos --}}
            @if(!empty($issue->image_path))
              <div class="mb-3">
                <div class="small fw-bold text-secondary mb-1.5">{{ __('messages.7s_v2_violation_photos') }}</div>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($issue->image_path as $imgPath)
                    <a href="{{ asset($imgPath) }}" target="_blank" class="border rounded-3 overflow-hidden d-block shadow-sm" style="width: 100px; height: 100px;">
                      <img src="{{ asset($imgPath) }}" alt="Ảnh vi phạm" style="width:100%; height:100%; object-fit: cover;">
                    </a>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- Remediation Section (Khắc phục) --}}
            <div class="border-top pt-3 mt-3">
              <h6 class="fw-bold text-primary mb-2 d-flex align-items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16"><path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.969-.969 2.675-2.675a1 1 0 0 1 .708-.293h.07a1 1 0 0 0 .815-.419L16 1 15 0l-3.081 2.2a1 1 0 0 0-.419.815v.07a1 1 0 0 1-.293.708L8.53 6.468 6.468 4.406l2.675-2.675a1 1 0 0 1 .293-.708v-.07a1 1 0 0 0-.419-.815L8 0 7 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293L8.53 6.468 7.07 5.008 4.406 2.344a1 1 0 0 1-.293-.708v-.07a1 1 0 0 0-.419-.815L3 0 2 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l-.46.46-2.675-2.675A1 1 0 0 1 2.344.406v-.07a1 1 0 0 0-.419-.815L1 0z"/></svg>
                <span>{{ __('messages.7s_v2_remediation_section_title') }}</span>
              </h6>

              {{-- If previously rejected --}}
              @if($issue->status === 'rejected' && $issue->rejection_reason)
                <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                  {{ __('messages.7s_v2_auditor_rejected_alert', ['reason' => $issue->rejection_reason]) }}
                </div>
              @endif

              {{-- If already improved or verified, show improvement details --}}
              @if(in_array($issue->status, ['improved', 'verified']))
                <div class="bg-light p-3 rounded-3 border mb-3">
                  <div class="small fw-semibold text-secondary mb-1">
                    {{ __('messages.7s_v2_improved_content_by', ['user' => $issue->improver->name ?? 'Người phụ trách', 'time' => $issue->improved_at ? $issue->improved_at->format('d/m/Y H:i') : '']) }}
                  </div>
                  <div class="text-dark mb-2">{{ $issue->improvement_note }}</div>

                  @if(!empty($issue->improvement_image_path))
                    <div class="d-flex flex-wrap gap-2 mt-2">
                      @foreach($issue->improvement_image_path as $impPath)
                        <a href="{{ asset($impPath) }}" target="_blank" class="border rounded-3 overflow-hidden d-block shadow-sm" style="width: 100px; height: 100px;">
                          <img src="{{ asset($impPath) }}" alt="Ảnh khắc phục" style="width:100%; height:100%; object-fit: cover;">
                        </a>
                      @endforeach
                    </div>
                  @endif
                </div>

                {{-- Verification result --}}
                @if($issue->status === 'verified')
                  <div class="alert alert-success py-2 px-3 small rounded-3 mb-0 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                    <div>
                      {{ __('messages.7s_v2_verified_badge_desc', ['user' => $issue->verifier->name ?? 'Auditor', 'time' => $issue->verified_at ? $issue->verified_at->format('d/m/Y H:i') : '']) }}
                    </div>
                  </div>
                @elseif($isInspectorOrAdmin)
                  {{-- Inspector Verification Form --}}
                  <div class="card border border-info border-opacity-50 rounded-3 p-3 bg-white mt-2">
                    <div class="fw-bold small text-primary mb-2">{{ __('messages.7s_v2_auditor_eval_title') }}</div>
                    <form action="{{ route('seven-s-v2.verify', $issue->id) }}" method="POST">
                      @csrf
                      <div class="d-flex gap-2 mb-2">
                        <button type="submit" name="decision" value="verify" class="btn btn-success btn-sm rounded-3 fw-bold px-3">
                          {{ __('messages.7s_v2_btn_approve_remedy') }}
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-3 fw-bold px-3" onclick="document.getElementById('rejectBox-{{ $issue->id }}').classList.toggle('d-none')">
                          {{ __('messages.7s_v2_btn_reject_remedy') }}
                        </button>
                      </div>

                      <div id="rejectBox-{{ $issue->id }}" class="d-none mt-2 p-2 bg-light rounded-3 border">
                        <label class="form-label small fw-semibold text-danger mb-1">{{ __('messages.7s_v2_reject_reason_label') }}</label>
                        <textarea name="rejection_reason" class="form-control form-control-sm rounded-3 mb-2" rows="2" placeholder="{{ __('messages.7s_v2_reject_reason_placeholder') }}"></textarea>
                        <button type="submit" name="decision" value="reject" class="btn btn-danger btn-sm rounded-3 fw-semibold">
                          {{ __('messages.7s_v2_btn_confirm_reject') }}
                        </button>
                      </div>
                    </form>
                  </div>
                @else
                  <div class="text-info small fw-medium">
                    {{ __('messages.7s_v2_waiting_auditor_notice') }}
                  </div>
                @endif

              {{-- If pending or rejected -> show submission form for Department / Admin --}}
              @else
                <div class="card border border-warning border-opacity-50 rounded-3 p-3 bg-light bg-opacity-50">
                  <div class="small fw-bold text-dark mb-2">{{ __('messages.7s_v2_submit_remedy_title') }}</div>
                  <form action="{{ route('seven-s-v2.improve', $issue->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                      <textarea name="improvement_note" class="form-control form-control-sm rounded-3" rows="2" placeholder="{{ __('messages.7s_v2_remedy_placeholder') }}" required></textarea>
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                      <input type="file" name="images[]" multiple accept="image/*" class="form-control form-control-sm rounded-3" style="max-width: 300px;">
                      <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-semibold px-3">
                        {{ __('messages.7s_v2_btn_send_remedy') }}
                      </button>
                    </div>
                  </form>
                </div>
              @endif

            </div>

          </div>
        </div>
      @endforeach
    </div>
  @endif

</div>
@endsection
