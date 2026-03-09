@extends('layouts.app-simple')
@section('title', __('messages.7s_record_title') . $record->id)

@section('content')
@php
$nonBResults = $record->results->where('grade', '!=', 'B');
$isFullyImproved = $nonBResults->isNotEmpty() && $nonBResults->every(fn($r) => !empty($r->improvement_note));
$canImprove = (
(auth()->user()->hasRole('admin') || (auth()->user()->hasRole('7s') && auth()->user()->managed_department === 'XNK'))
&& $nonBResults->isNotEmpty()
&& $nonBResults->contains(fn($r) => !$r->improvement_note)
);
@endphp

<div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('seven-s.index') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            {{ __('messages.7s_back') }}
        </a>
        <h4 class="mb-0 fw-bold">{{ __('messages.7s_record_title') }}{{ $record->id }}</h4>
        @if($isFullyImproved)
        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
            ✅ {{ __('messages.7s_fully_improved_badge') }}
        </span>
        @endif
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($canImprove)
        <button type="button" class="btn btn-sm btn-warning d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#singleImprovementModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9" />
                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
            </svg>
            {{ __('messages.7s_improve_btn') }}
        </button>
        @endif
        @php
        $canEdit = !$isFullyImproved && (
        auth()->user()->hasRole('admin') ||
        (auth()->user()->hasRole('7s') && empty(auth()->user()->managed_department) && $record->inspector_id === auth()->id())
        );
        @endphp
        @if($canEdit)
        <a href="{{ route('seven-s.edit', $record->id) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            {{ __('messages.7s_edit_btn') }}
        </a>
        @endif
        <a href="{{ route('seven-s.export', $record->id) }}"
            class="btn btn-sm btn-outline-success d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            {{ __('messages.7s_export_excel') }}
        </a>
        @if(auth()->user()->hasRole('admin'))
        <form action="{{ route('seven-s.destroy', $record->id) }}" method="POST" onsubmit="return confirm(`{{ __('messages.7s_delete_confirm') }}`)">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    <path d="M10 11v6" />
                    <path d="M14 11v6" />
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                </svg>
                {{ __('messages.7s_delete_btn') }}
            </button>
        </form>
        @endif
    </div>
</div>

@if($isFullyImproved)
<div class="alert border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-3" style="background:#d1fae5; border-left: 4px solid #10b981 !important;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
    </svg>
    <div>
        <div class="fw-bold text-success">{{ __('messages.7s_fully_improved_title') }}</div>
        <div class="text-secondary small">{{ __('messages.7s_fully_improved_desc') }}</div>
    </div>
</div>
@endif

@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">{{ session('success') }}</div>
@endif

{{-- Score summary card --}}
@php
$pct = $record->max_score > 0 ? round(($record->score / $record->max_score) * 100) : 0;
$color = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
@endphp
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-4 d-flex align-items-center gap-4 flex-wrap">
        <div>
            <div class="text-muted small text-uppercase fw-bold mb-1">{{ __('messages.7s_department') }}</div>
            <span class="badge bg-info text-dark fs-6 px-3 py-2">{{ $record->department }}</span>
        </div>
        <div>
            <div class="text-muted small text-uppercase fw-bold mb-1">{{ __('messages.7s_inspector_label') }}</div>
            <div class="fw-bold">{{ $record->inspector->name ?? '—' }}</div>
        </div>
        <div>
            <div class="text-muted small text-uppercase fw-bold mb-1">{{ __('messages.7s_inspection_date') }}</div>
            <div class="fw-bold">{{ $record->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="ms-auto text-center">
            <div class="display-4 fw-bold text-{{ $color }}">{{ $record->score }}</div>
            <div class="text-muted small">/ {{ $record->max_score }} {{ __('messages.7s_score_label') }}</div>
            <div class="badge bg-{{ $color }} px-3 py-2 mt-1 fs-6">{{ $pct }}%</div>
        </div>
    </div>
    <div class="px-4 pb-3">
        <div class="progress" style="height:10px; border-radius:99px;">
            <div class="progress-bar bg-{{ $color }}" style="width: {{ $pct }}%"></div>
        </div>
    </div>
</div>

{{-- Results grouped by section --}}
@php $grouped = $record->results->groupBy(fn($r) => $r->checklist?->section ?? 'Khác') @endphp
@foreach($grouped as $section => $results)
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header fw-bold bg-dark text-white py-3 px-4">{{ $section }}</div>
    <div class="card-body p-0">
        @foreach($results as $result)
        @php
        $gradeColors = ['B' => 'success', 'C' => 'warning', 'D' => 'danger', 'E' => 'dark'];
        $gradeColor = $gradeColors[$result->grade] ?? 'secondary';
        $gradeLabels = ['B' => __('messages.7s_grade_good_short'), 'C' => __('messages.7s_grade_acceptable_short'), 'D' => __('messages.7s_grade_fail_short'), 'E' => __('messages.7s_grade_poor_short')];
        @endphp
        <div class="p-4 @if(!$loop->last) border-bottom @endif">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <span class="badge bg-secondary me-1">{{ $result->checklist?->sort_order }}</span>
                    <span class="fw-semibold">{{ $result->checklist?->content }}</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="badge bg-{{ $gradeColor }} px-3 py-2">{{ $result->grade }} — {{ $gradeLabels[$result->grade] }}</span>
                    <span class="fw-bold text-{{ $result->points >= 0 ? ($result->points > 0 ? 'success' : 'secondary') : 'danger' }}">
                        {{ $result->points > 0 ? '+' : '' }}{{ $result->points }}đ
                    </span>
                </div>
            </div>
            @if($result->note)
            <div class="mt-2 p-2 bg-light rounded-3 text-muted small">
                📝 {{ $result->note }}
            </div>
            @endif
            @if(!empty($result->image_path))
            <div class="mt-2 d-flex flex-wrap gap-2">
                @foreach((array)$result->image_path as $img)
                <a href="/{{ $img }}" target="_blank">
                    <img src="/{{ $img }}" class="img-thumbnail rounded" style="width:70px;height:70px;object-fit:cover;" alt="Ảnh lỗi">
                </a>
                @endforeach
            </div>
            @endif

            {{-- Improvement Section --}}
            @if($result->grade !== 'B')
            @if($result->improvement_note)
            <div class="mt-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded p-3 text-dark mb-3">
                <h6 class="fw-bold text-warning mb-3 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                    </svg>
                    {{ __('messages.7s_improvement_modal_title') }}
                </h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="text-muted small fw-bold mb-1">{{ __('messages.7s_improvement_note_label') }}</div>
                        <div style="white-space: pre-wrap;">{{ $result->improvement_note }}</div>
                    </div>

                    @if(!empty($result->improvement_image_path))
                    <div class="col-md-12 mt-3">
                        <div class="text-muted small fw-bold mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                            {{ __('messages.7s_improvement_img_label') }}
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach((array)$result->improvement_image_path as $img)
                            <a href="/{{ $img }}" target="_blank" class="d-inline-block position-relative rounded overflow-hidden shadow-sm" style="border: 2px solid #e2e8f0; width: 120px; height: 120px;">
                                <img src="/{{ $img }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="Ảnh cải thiện">
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12 mt-3">
                        <div class="d-flex align-items-center gap-3 text-muted small">
                            <span>👤 Người cải thiện: <strong class="text-dark">{{ $result->improver->name ?? 'Unknown' }}</strong></span>
                            <span>🕒 Thời gian: <strong>{{ \Carbon\Carbon::parse($result->improved_at)->format('H:i d/m/Y') }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            @if(auth()->user()->hasRole('admin') || (auth()->user()->hasRole('7s') && auth()->user()->managed_department === $record->department))
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#improveModal{{ $result->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                    </svg>
                    {{ __('messages.7s_improve_action_btn') }}
                </button>
            </div>
            @endif
            @endif

            {{-- Improve Modal --}}
            @if(!$result->improvement_note && (auth()->user()->hasRole('admin') || (auth()->user()->hasRole('7s') && auth()->user()->managed_department === $record->department)))
            <div class="modal fade" id="improveModal{{ $result->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form action="{{ route('seven_s.improve', $result->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-warning bg-opacity-10 border-bottom-0 pb-0">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                    <svg class="text-warning" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                    {{ __('messages.7s_improve_action_btn') }} 7S
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="bg-light rounded p-3 mb-4 text-muted small border">
                                    <strong>{{ __('messages.7s_improvement_status') }}:</strong> {{ $result->checklist?->content }}
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark">{{ __('messages.7s_improvement_note_label') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="improvement_note" rows="4" required placeholder="{{ __('messages.7s_improvement_note_placeholder') }}"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">{{ __('messages.7s_improvement_photo_label') }}</label>
                                    <input type="file" class="form-control" name="improvement_images[]" multiple accept="image/*">
                                    <div class="form-text mt-2 text-muted">{{ __('messages.7s_improvement_photo_hint') }}</div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                <button type="submit" class="btn btn-warning fw-bold px-4">{{ __('messages.7s_improvement_save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @endif

        </div>
        @endforeach
    </div>
</div>
@endforeach

@if($canImprove)
{{-- Single Combined Improvement Modal (like Audit) --}}
<div class="modal fade" id="singleImprovementModal" tabindex="-1" aria-labelledby="singleImprovementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('seven_s.improvements', $record->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-warning bg-opacity-10 border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="singleImprovementModalLabel">
                    <svg class="text-warning" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                    </svg>
                    {{ __('messages.7s_improvement_modal_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted mb-4">{{ __('messages.7s_improvement_modal_desc') }}</p>

                @foreach($nonBResults->filter(fn($r) => !$r->improvement_note) as $result)
                <div class="card bg-light border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold border-0 py-3">
                        <div class="d-flex gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-1">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <div>
                                <div class="fs-6">{{ $result->checklist?->content }}</div>
                                <div class="fw-normal small mt-1">Điểm: <span class="badge bg-{{ $result->grade === 'C' ? 'warning' : ($result->grade === 'D' ? 'danger' : 'dark') }}">{{ $result->grade }}</span>
                                    @if($result->note) — {{ $result->note }} @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.7s_improvement_note_label') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-white" name="improvements[{{ $result->id }}][improvement_note]" rows="2" required placeholder="{{ __('messages.7s_improvement_note_placeholder') }}"></textarea>
                        </div>
                        <div>
                            <label class="form-label fw-bold">{{ __('messages.7s_improvement_photo_label') }}</label>
                            <input type="file" class="form-control bg-white" name="improvements[{{ $result->id }}][improvement_images][]" multiple accept="image/*">
                            <div class="form-text">{{ __('messages.7s_improvement_photo_hint') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm">{{ __('messages.7s_improvement_save') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection