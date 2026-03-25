@extends('layouts.app-simple')
@section('title', __('messages.7s_edit_title') . $record->id)

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('seven-s.show', $record->id) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">✏️ {{ __('messages.7s_edit_title') }}{{ $record->id }}</h4>
    <span class="badge bg-info text-dark">{{ $record->department }}</span>
</div>

<div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
        <line x1="12" y1="9" x2="12" y2="13" />
        <line x1="12" y1="17" x2="12.01" y2="17" />
    </svg>
    <span>{{ __('messages.7s_edit_locked_warning') }}</span>
</div>

@if ($errors->any())
<div class="alert alert-danger border-0 rounded-3 mb-4">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('seven-s.update', $record->id) }}" method="POST" enctype="multipart/form-data" id="sevenSEditForm">
    @csrf
    @method('PUT')

    @foreach($checklist as $section => $items)
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header fw-bold bg-dark text-white py-3 px-4 fs-6">{{ __($section) }}</div>
        <div class="card-body p-0">
            @foreach($items as $item)
            @php
            $existing = $existingResults[$item->id] ?? null;
            $currentGrade = old("grades.{$item->id}", $existing?->grade ?? '');
            $currentNote = old("notes.{$item->id}", $existing?->note ?? '');
            $isImproved = $existing && !empty($existing->improvement_note);
            $isResponded = $existing && !is_null($existing->department_agreement);
            $isLocked = $isResponded || ($isImproved && $existing->grade !== 'B');
            @endphp
            <div class="p-4 @if(!$loop->last) border-bottom @endif {{ $isLocked ? 'bg-light' : '' }}" id="item_{{ $item->id }}">
                <div class="mb-3 d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <span class="badge bg-secondary me-2">{{ $item->sort_order }}</span>
                        <span class="fw-semibold text-dark">{{ __($item->content) }}</span>
                    </div>
                    @if($isLocked)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success flex-shrink-0">
                        ✅ {{ __('messages.7s_edit_locked_badge') }}
                    </span>
                    @endif
                </div>

                @if($isLocked)
                {{-- Locked: show read-only grade and note, hidden input to preserve value --}}
                <input type="hidden" name="grades[{{ $item->id }}]" value="{{ $existing->grade }}">
                <input type="hidden" name="notes[{{ $item->id }}]" value="{{ $existing->note }}">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @foreach(['B' => [__('messages.7s_grade_good'), 'success'], 'C' => [__('messages.7s_grade_acceptable'), 'warning'], 'D' => [__('messages.7s_grade_fail'), 'danger'], 'E' => [__('messages.7s_grade_poor'), 'dark']] as $grade => [$label, $color])
                    <span class="btn btn-{{ $color }} btn-sm rounded-3 px-3 py-2 fw-bold {{ $existing->grade === $grade ? '' : 'opacity-25' }}" style="pointer-events:none;">
                        {{ $label }}
                    </span>
                    @endforeach
                </div>
                @if($existing->note)
                <div class="p-2 bg-white border rounded-2 text-muted small mb-2">{{ $existing->note }}</div>
                @endif
                @else
                {{-- Editable grade --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach(['B' => [__('messages.7s_grade_good'), 'success', '+2đ'], 'C' => [__('messages.7s_grade_acceptable'), 'warning', '+1đ'], 'D' => [__('messages.7s_grade_fail'), 'danger', '0đ'], 'E' => [__('messages.7s_grade_poor'), 'dark', '-5đ']] as $grade => [$label, $color, $pts])
                    <div>
                        <input type="radio" class="btn-check grade-radio"
                            id="grade_{{ $item->id }}_{{ $grade }}"
                            name="grades[{{ $item->id }}]"
                            value="{{ $grade }}"
                            data-item="{{ $item->id }}"
                            {{ $currentGrade === $grade ? 'checked' : '' }}
                            required>
                        <label for="grade_{{ $item->id }}_{{ $grade }}"
                            class="btn btn-outline-{{ $color }} rounded-3 px-3 py-2 fw-bold">
                            {{ $label }}<br><small class="opacity-75">{{ $pts }}</small>
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Note + photo (shown for C/D/E) --}}
                <div class="fail-detail {{ in_array($currentGrade, ['C','D','E']) ? '' : 'd-none' }}" id="detail_{{ $item->id }}">
                    <div class="mb-3">
                        <label class="form-label text-danger fw-semibold small">{{ __('messages.7s_note_required') }}</label>
                        <textarea class="form-control bg-light"
                            name="notes[{{ $item->id }}]"
                            rows="2"
                            placeholder="{{ __('messages.7s_note_placeholder') }}">{{ $currentNote }}</textarea>
                    </div>

                    {{-- Existing images with red-X remove buttons (like Audit) --}}
                    @if($existing && !empty($existing->image_path))
                    <div class="mb-2">
                        <div class="text-muted small fw-semibold mb-2">{{ __('messages.7s_current_images') }}</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach((array)$existing->image_path as $imgPath)
                            <div class="position-relative current-image-thumb" style="width:80px;height:80px;">
                                <a href="/{{ $imgPath }}" target="_blank">
                                    <img src="/{{ $imgPath }}" class="img-thumbnail w-100 h-100 rounded" style="object-fit:cover;" alt="Ảnh">
                                </a>
                                <button type="button"
                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center remove-img-btn"
                                    data-item-id="{{ $item->id }}"
                                    data-path="{{ $imgPath }}"
                                    style="width:20px;height:20px;transform:translate(40%,-40%);font-size:12px;">&times;</button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Add new images --}}
                    <div>
                        <label class="form-label text-secondary fw-semibold small">{{ __('messages.7s_add_new_images') }}</label>
                        <div class="photo-upload-wrapper" data-item="{{ $item->id }}">
                            <div class="preview-container d-flex flex-wrap gap-2 mb-2"></div>
                            <div class="hidden-inputs-container"></div>
                            <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 py-2 btn-add-photo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <line x1="12" y1="8" x2="12" y2="16" />
                                    <line x1="8" y1="12" x2="16" y2="12" />
                                </svg>
                                {{ __('messages.7s_add_photo') }}
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div style="height:90px;"></div>
    <div class="fixed-bottom container p-3 bg-white border-top shadow-lg" style="max-width:1100px;">
        <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold fs-5 text-uppercase d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
            </svg>
            {{ __('messages.7s_save_edit') }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide note+photo area based on selected grade
        document.querySelectorAll('.grade-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const itemId = this.dataset.item;
                const detail = document.getElementById('detail_' + itemId);
                if (['C', 'D', 'E'].includes(this.value)) {
                    detail.classList.remove('d-none');
                } else {
                    detail.classList.add('d-none');
                }
            });
        });

        // Remove existing image (Audit style: fade + hidden input)
        document.querySelectorAll('.remove-img-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const thumb = this.closest('.current-image-thumb');
                const path = this.dataset.path;
                const itemId = this.dataset.itemId;

                // Create hidden input so controller knows this path should be removed
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `remove_images[${itemId}][]`;
                hiddenInput.value = path;
                document.getElementById('sevenSEditForm').appendChild(hiddenInput);

                // Visual feedback: hide thumbnail immediately
                thumb.style.display = 'none';
            });
        });

        // Add new photos
        document.querySelectorAll('.btn-add-photo').forEach(btn => {
            btn.addEventListener('click', function() {
                const wrapper = this.closest('.photo-upload-wrapper');
                const itemId = wrapper.dataset.item;
                const hiddenInputsContainer = wrapper.querySelector('.hidden-inputs-container');
                const previewContainer = wrapper.querySelector('.preview-container');

                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `images[${itemId}][]`;
                fileInput.accept = 'image/*';
                fileInput.capture = 'environment';
                fileInput.style.display = 'none';

                fileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = e => {
                                const thumbWrapper = document.createElement('div');
                                thumbWrapper.className = 'position-relative d-inline-block';
                                thumbWrapper.style.cssText = 'width:80px;height:80px;';

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'img-thumbnail w-100 h-100 object-fit-cover rounded';

                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center';
                                removeBtn.style.cssText = 'width:20px;height:20px;transform:translate(40%,-40%);font-size:12px;';
                                removeBtn.innerHTML = '&times;';
                                removeBtn.addEventListener('click', () => {
                                    thumbWrapper.remove();
                                    fileInput.remove();
                                });

                                thumbWrapper.appendChild(img);
                                thumbWrapper.appendChild(removeBtn);
                                previewContainer.appendChild(thumbWrapper);
                            };
                            reader.readAsDataURL(file);
                        });
                        hiddenInputsContainer.appendChild(fileInput);
                    }
                });
                fileInput.click();
            });
        });
    });
</script>

<style>
    .btn-check:checked+.btn-outline-success {
        background: #198754;
        color: #fff;
    }

    .btn-check:checked+.btn-outline-warning {
        background: #ffc107;
        color: #000;
    }

    .btn-check:checked+.btn-outline-danger {
        background: #dc3545;
        color: #fff;
    }

    .btn-check:checked+.btn-outline-dark {
        background: #212529;
        color: #fff;
    }
</style>
@endsection