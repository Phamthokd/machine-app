@extends('layouts.app-simple')
@section('title', __('messages.7s_create_form'))

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('seven-s.index') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">📋 {{ __('messages.7s_create_form') }} - <span class="text-primary">{{ $department }}</span></h4>
</div>

@if ($errors->any())
<div class="alert alert-danger border-0 rounded-3 mb-4">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('seven-s.store') }}" method="POST" enctype="multipart/form-data" id="sevenSForm">
    @csrf
    <input type="hidden" name="department" value="{{ $department }}">

    @foreach($checklist as $section => $items)
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header fw-bold bg-dark text-white py-3 px-4 fs-6">
            {{ $section }}
        </div>
        <div class="card-body p-0">
            @foreach($items as $index => $item)
            <div class="p-4 @if(!$loop->last) border-bottom @endif" id="item_{{ $item->id }}">
                <div class="mb-3">
                    <span class="badge bg-secondary me-2">{{ $item->sort_order }}</span>
                    <span class="fw-semibold text-dark">{{ $item->content }}</span>
                </div>

                {{-- Grade radio buttons --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach(['B' => [__('messages.7s_grade_good'), 'success', '+2đ'], 'C' => [__('messages.7s_grade_acceptable'), 'warning', '+1đ'], 'D' => [__('messages.7s_grade_fail'), 'danger', '0đ'], 'E' => [__('messages.7s_grade_poor'), 'dark', '-5đ']] as $grade => [$label, $color, $pts])
                    <div>
                        <input type="radio" class="btn-check grade-radio"
                            id="grade_{{ $item->id }}_{{ $grade }}"
                            name="grades[{{ $item->id }}]"
                            value="{{ $grade }}"
                            data-item="{{ $item->id }}"
                            {{ old("grades.{$item->id}") === $grade ? 'checked' : '' }}
                            required>
                        <label for="grade_{{ $item->id }}_{{ $grade }}"
                            class="btn btn-outline-{{ $color }} rounded-3 px-3 py-2 fw-bold">
                            {{ $label }}<br><small class="opacity-75">{{ $pts }}</small>
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Note + photo section (shown for C/D/E) --}}
                <div class="fail-detail d-none" id="detail_{{ $item->id }}">
                    <div class="mb-3">
                        <label class="form-label text-danger fw-semibold small d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            {{ __('messages.7s_note_required') }}
                        </label>
                        <textarea class="form-control bg-light"
                            name="notes[{{ $item->id }}]"
                            rows="2"
                            placeholder="{{ __('messages.7s_note_placeholder') }}">{{ old("notes.{$item->id}") }}</textarea>
                    </div>
                    <div>
                        <label class="form-label text-secondary fw-semibold small d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                            {{ __('messages.7s_photo_optional') }}
                        </label>
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
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div style="height:90px;"></div>
    <div class="fixed-bottom container p-3 bg-white border-top shadow-lg" style="max-width:1100px;">
        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 text-uppercase d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
            </svg>
            {{ __('messages.7s_save_record') }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide detail section based on grade
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

        // Restore state on old() reload
        document.querySelectorAll('.grade-radio:checked').forEach(radio => {
            if (['C', 'D', 'E'].includes(radio.value)) {
                document.getElementById('detail_' + radio.dataset.item)?.classList.remove('d-none');
            }
        });

        // Photo upload
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