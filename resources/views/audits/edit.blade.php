@extends('layouts.app-simple')
@section('title', 'Sửa phiếu đánh giá')

@section('content')
<div class="mb-4">
    <a href="{{ route('audits.show', $audit->id) }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6" />
        </svg>
        {{ __('messages.back') }}
    </a>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Header card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="text-white-50 text-uppercase fw-bold small mb-1">{{ __('messages.audit_template_label') }}</div>
            <div class="h5 fw-bold mb-0">{{ __($audit->template->name) }}</div>
            <div class="mt-2 text-white-50 small">{{ __('messages.department') }}: <strong class="text-info">{{ __('messages.' . $audit->template->department_name) }}</strong></div>
        </div>
    </div>

    <form action="{{ route('audits.update', $audit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-0">
                @forelse($audit->results as $index => $result)
                <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                    {{-- Số thứ tự + câu hỏi --}}
                    <div class="mb-3">
                        <h5 class="fw-bold text-dark lh-base">
                            <span class="bg-light text-secondary rounded px-2 py-1 me-2 fs-6">{{ $index + 1 }}</span>
                            {{ $result->criterion ? __($result->criterion->content) : 'Câu hỏi đã bị xoá' }}
                        </h5>
                    </div>

                    {{-- Nút Đạt / Lỗi (giống create.blade.php) --}}
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        {{-- ĐẠT (V) --}}
                        <input type="radio" class="btn-check audit-radio-pass" id="pass_{{ $result->id }}"
                            name="results[{{ $result->id }}][is_passed]" value="1" autocomplete="off"
                            {{ $result->is_passed ? 'checked' : '' }}>
                        <label class="audit-btn btn btn-outline-primary d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-3"
                            for="pass_{{ $result->id }}" style="max-width: 200px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span class="fw-bold fs-5">{{ __('messages.audit_pass') }}</span>
                        </label>

                        {{-- LỖI (X) --}}
                        <input type="radio" class="btn-check audit-radio-fail" id="fail_{{ $result->id }}"
                            name="results[{{ $result->id }}][is_passed]" value="0" autocomplete="off"
                            {{ !$result->is_passed ? 'checked' : '' }}>
                        <label class="audit-btn btn btn-outline-danger d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-3"
                            for="fail_{{ $result->id }}" style="max-width: 200px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            <span class="fw-bold fs-5">{{ __('messages.audit_fail') }}</span>
                        </label>
                    </div>

                    {{-- Khu vực lỗi: chỉ hiện khi chọn Lỗi --}}
                    <div class="note-container" @if($result->is_passed) style="display: none;" @endif>
                        {{-- Ghi chú lỗi --}}
                        <div class="mb-3">
                            <label class="form-label text-danger fw-semibold d-flex align-items-center gap-1 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                {{ __('messages.error_note_required') }}
                            </label>
                            <textarea class="form-control bg-light" name="results[{{ $result->id }}][note]" rows="2"
                                placeholder="{{ __('messages.error_note_placeholder') }}">{{ old("results.{$result->id}.note", $result->note) }}</textarea>
                        </div>

                        {{-- Ảnh đính kèm (Option) --}}
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary d-flex align-items-center gap-1 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <polyline points="21 15 16 10 5 21" />
                                </svg>
                                {{ __('messages.attached_image_optional') }}
                            </label>

                            {{-- Ảnh đang có --}}
                            @if(!empty($result->image_path))
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach((array)$result->image_path as $path)
                                <div class="position-relative current-image-thumb" style="width:80px; height:80px;">
                                    <a href="/{{ $path }}" target="_blank">
                                        <img src="/{{ $path }}" class="img-thumbnail w-100 h-100 rounded" style="object-fit:cover;" alt="Ảnh lỗi">
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center remove-img-btn"
                                        data-result-id="{{ $result->id }}"
                                        data-path="{{ $path }}"
                                        style="width:20px;height:20px;transform:translate(40%,-40%);font-size:12px;">&times;</button>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- Thêm ảnh mới (giống create.blade.php) --}}
                            <div class="photo-upload-wrapper mt-3" data-result-id="{{ $result->id }}">
                                <div class="preview-container d-flex flex-wrap gap-2 mb-3"></div>
                                <div class="hidden-inputs-container"></div>
                                <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2 btn-add-photo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <line x1="12" y1="8" x2="12" y2="16" />
                                        <line x1="8" y1="12" x2="16" y2="12" />
                                    </svg>
                                    <span>{{ __('messages.add_photo') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-5 text-center text-muted">{{ __('messages.no_questions_defined') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Footer Spacer --}}
        <div style="height: 100px;"></div>

        {{-- Submit Button (giống create.blade.php) --}}
        <div class="fixed-bottom container p-3 bg-white border-top shadow-lg" style="max-width: 1100px;">
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 text-uppercase d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radioPassBtns = document.querySelectorAll('.audit-radio-pass');
        const radioFailBtns = document.querySelectorAll('.audit-radio-fail');

        function handleRadioChange(e) {
            const isFail = e.target.classList.contains('audit-radio-fail');
            const container = e.target.closest('.p-4');
            const noteContainer = container.querySelector('.note-container');
            const textarea = noteContainer.querySelector('textarea');

            if (isFail) {
                noteContainer.style.display = 'block';
                textarea.focus();
            } else {
                noteContainer.style.display = 'none';
            }
        }

        radioPassBtns.forEach(btn => btn.addEventListener('change', handleRadioChange));
        radioFailBtns.forEach(btn => btn.addEventListener('change', handleRadioChange));

        // Xóa ảnh hiện tại
        document.querySelectorAll('.remove-img-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const thumb = this.closest('.current-image-thumb');
                const path = this.dataset.path;
                const resultId = this.dataset.resultId;

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `results[${resultId}][image_remove][]`;
                hiddenInput.value = path;
                this.closest('form').appendChild(hiddenInput);

                thumb.style.opacity = '0.3';
                thumb.style.pointerEvents = 'none';
                this.textContent = '✓';
                this.classList.replace('btn-danger', 'btn-secondary');
            });
        });

        // Thêm ảnh mới (giống create.blade.php)
        document.querySelectorAll('.btn-add-photo').forEach(btn => {
            btn.addEventListener('click', function() {
                const wrapper = this.closest('.photo-upload-wrapper');
                const resultId = wrapper.dataset.resultId;
                const hiddenInputsContainer = wrapper.querySelector('.hidden-inputs-container');
                const previewContainer = wrapper.querySelector('.preview-container');

                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `results[${resultId}][image][]`;
                fileInput.accept = 'image/*';
                fileInput.capture = 'environment';
                fileInput.style.display = 'none';

                fileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const thumbWrapper = document.createElement('div');
                                thumbWrapper.className = 'position-relative d-inline-block';
                                thumbWrapper.style.width = '80px';
                                thumbWrapper.style.height = '80px';

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'img-thumbnail w-100 h-100 object-fit-cover rounded';

                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center';
                                removeBtn.style.width = '20px';
                                removeBtn.style.height = '20px';
                                removeBtn.style.transform = 'translate(40%, -40%)';
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
    /* Giống create.blade.php */
    .btn-check:checked+.audit-btn {
        opacity: 1;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .btn-check:not(:checked)+.audit-btn {
        opacity: .6;
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #64748b;
    }

    .btn-check:not(:checked)+.btn-outline-primary:hover {
        color: #0d6efd;
        border-color: #0d6efd;
        opacity: .8;
    }

    .btn-check:not(:checked)+.btn-outline-danger:hover {
        color: #dc3545;
        border-color: #dc3545;
        opacity: .8;
    }

    .audit-btn {
        transition: all .2s cubic-bezier(.4, 0, .2, 1);
    }

    .current-image-thumb {
        transition: opacity .3s;
    }
</style>
@endsection