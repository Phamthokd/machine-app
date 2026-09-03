@extends('layouts.app-simple')
@section('title', __('messages.complete_repair_ticket'))

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="/repair-requests" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6" />
            </svg>
            {{ __('messages.back') }}
        </a>
        <h3 class="fw-bold mb-1">{{ __('messages.accept_repair') }}</h3>
        <p class="text-secondary mb-0">{{ __('messages.complete_repair_info') }} #{{ $repair->code }}</p>
    </div>

    <!-- Machine Info Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">{{ __('messages.machine_code') }}</div>
                    <div class="h3 fw-bold mb-1">{{ $machine->ma_thiet_bi }}</div>
                    <div class="fw-medium text-white-50">{{ $machine->ten_thiet_bi }}</div>
                </div>
                <div class="text-end">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">{{ __('messages.department') }}</div>
                    <div class="h5 fw-bold mb-0">{{ $machine->department->name }}</div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-danger">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <div class="fw-bold">{{ __('messages.please_check_again') }}</div>
        </div>
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('repairs.update_completed', $repair->id) }}" method="POST" enctype="multipart/form-data" id="repairEditCompletedForm">
        @csrf
        @method('PUT')

        <!-- Issue Details -->
        <div class="form-section">
            <div class="section-title">
                {{ __('messages.issue_details') }}
            </div>

            @if($repair->type == 'contractor')
            <!-- CONTRACTOR FORM -->
            <div class="mb-3">
                <label class="form-label">{{ __('messages.damage_cause') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" name="nguyen_nhan" required>{{ old('nguyen_nhan', $repair->nguyen_nhan) }}</textarea>
                <div class="form-text">{{ __('messages.contractor_cause_hint') }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.repair_content') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" name="noi_dung_sua_chua" placeholder="{{ __('messages.contractor_repair_hint') }}" required>{{ old('noi_dung_sua_chua', $repair->noi_dung_sua_chua) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.supporter_optional') }}</label>
                <select class="form-select" name="nguoi_ho_tro[]" multiple style="min-height: 100px;">
                    @php
                        $selected = old('nguoi_ho_tro', $repair->nguoi_ho_tro ?? '');
                        if (!is_array($selected)) {
                            $selected = explode(', ', $selected);
                        }
                    @endphp
                    @foreach($contractors as $c)
                    <option value="{{ $c->name }}" @selected(in_array($c->name, $selected))>{{ $c->name }}</option>
                    @endforeach
                </select>
                <div class="form-text text-muted" style="font-size: 0.75rem;">💡 Giữ phím Ctrl (hoặc Command trên Mac) để chọn nhiều người.</div>
            </div>

            <!-- Contractor Photo Upload / Camera Capture (Optional) -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        {{ __('messages.repair_completion_photos') }}
                    </label>
                    <span class="badge bg-light text-secondary border fw-normal">{{ __('messages.optional') }}</span>
                </div>
                <div class="text-muted small mb-2">{{ __('messages.repair_photos_optional_hint') }}</div>

                {{-- Existing images if any --}}
                @if(!empty($repair->images) && count($repair->images) > 0)
                <div class="mb-2">
                    <div class="text-muted small fw-semibold mb-1">{{ __('messages.current_photos') }}:</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($repair->images as $img)
                        <div class="position-relative current-image-thumb" style="width: 76px; height: 76px;">
                            <img src="/{{ $img }}" class="rounded-3 border w-100 h-100" style="object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute p-0 rounded-circle remove-existing-img" 
                                    style="top: -6px; right: -6px; width: 20px; height: 20px; font-size: 11px; line-height: 1; display: flex; align-items: center; justify-content: center;"
                                    data-img="{{ $img }}" title="Xóa ảnh này">×</button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="photo-upload-area" onclick="document.getElementById('photoFileInput').click()">
                    <div class="d-flex flex-column align-items-center justify-content-center py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0d6efd" stroke-width="1.8" class="mb-2">
                            <rect x="3" y="3" width="18" height="18" rx="3"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <span class="fw-bold text-primary" style="font-size: 0.9rem;">{{ __('messages.tap_to_add_photo') }}</span>
                        <span class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">{{ __('messages.multiple_photos_hint') }}</span>
                    </div>
                </div>
                <input type="file" id="photoFileInput" accept="image/*" multiple style="display:none;">
                <div class="photo-preview-grid" id="photoPreviewGrid"></div>
                <div id="hiddenImagesContainer"></div>
                <div id="removeImagesContainer"></div>
            </div>
            @else
            <!-- MECHANIC FORM -->
            <div class="mb-3">
                <label class="form-label">{{ __('messages.product_code') }} <span class="text-danger">*</span></label>
                <input class="form-control" name="ma_hang" value="{{ old('ma_hang', $repair->ma_hang == 'N/A' ? '' : $repair->ma_hang) }}" placeholder="{{ __('messages.product_code_hint') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.process_stage') }} <span class="text-danger">*</span></label>
                <input class="form-control" name="cong_doan" value="{{ old('cong_doan', $repair->cong_doan == 'N/A' ? '' : $repair->cong_doan) }}" placeholder="{{ __('messages.process_stage_hint') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.damage_cause') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" name="nguyen_nhan" required>{{ old('nguyen_nhan', $repair->nguyen_nhan) }}</textarea>
                <div class="form-text">{{ __('messages.mechanic_cause_hint') }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.repair_content') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" name="noi_dung_sua_chua" placeholder="{{ __('messages.mechanic_repair_hint') }}" required>{{ old('noi_dung_sua_chua', $repair->noi_dung_sua_chua) }}</textarea>
            </div>
            @endif
        </div>

        <!-- Time & Personnel -->
        <div class="form-section">
            <div class="section-title">
                {{ __('messages.time_personnel') }}
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.mechanic_label') }} <span class="text-danger">*</span></label>
                <select class="form-select" name="mechanic_id" required>
                    <option value="">{{ __('messages.select_mechanic') }}</option>
                    @foreach($mechanics as $mec)
                    <option value="{{ $mec->id }}" @selected(old('mechanic_id', $repair->mechanic_id) == $mec->id)>{{ $mec->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.start_time_label') }} <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" name="started_at" value="{{ old('started_at', $repair->started_at ? \Carbon\Carbon::parse($repair->started_at)->format('Y-m-d\TH:i') : '') }}" required>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <label class="form-label">{{ __('messages.completion_time_label') }} <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" name="ended_at" value="{{ old('ended_at', $repair->ended_at ? \Carbon\Carbon::parse($repair->ended_at)->format('Y-m-d\TH:i') : '') }}" required>
                </div>
            </div>

            @if($repair->type != 'contractor')
            <!-- Static QC Fields (Only for Mechanics) -->
            <div class="mb-3">
                <label class="form-label">{{ __('messages.endline_qc_triumph') }}</label>
                <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional') }}</span>
                <select class="form-select" name="endline_qc_name">
                    <option value="">{{ __('messages.select_endline_qc') }}</option>
                    <option value="Ánh" @selected(old('endline_qc_name', $repair->endline_qc_name) == 'Ánh')>Ánh</option>
                    <option value="Thuỷ" @selected(old('endline_qc_name', $repair->endline_qc_name) == 'Thuỷ')>Thuỷ</option>
                    <option value="Vân Anh" @selected(old('endline_qc_name', $repair->endline_qc_name) == 'Vân Anh')>Vân Anh</option>
                    <option value="Thanh" @selected(old('endline_qc_name', $repair->endline_qc_name) == 'Thanh')>Thanh</option>
                </select>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label">{{ __('messages.inline_qc_triumph') }}</label>
                    <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional') }}</span>
                </div>
                <select class="form-select" name="inline_qc_name">
                    <option value="">{{ __('messages.select_inline_qc') }}</option>
                    <option value="Sinh" @selected(old('inline_qc_name', $repair->inline_qc_name) == 'Sinh')>Sinh</option>
                    <option value="Chiêm" @selected(old('inline_qc_name', $repair->inline_qc_name) == 'Chiêm')>Chiêm</option>
                </select>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label">{{ __('messages.qa_supervisor_triumph') }}</label>
                    <span class="badge bg-light text-secondary fw-normal">{{ __('messages.optional') }}</span>
                </div>
                <select class="form-select" name="qa_supervisor_name">
                    <option value="" selected>{{ __('messages.select_qa') }}</option>
                    <option value="Tuyen" @selected(old('qa_supervisor_name', $repair->qa_supervisor_name) == 'Tuyen')>Tuyen</option>
                </select>
            </div>
            @endif
        </div>

        <!-- Spacer -->
        <div class="footer-spacer"></div>

        <!-- Submit Button -->
        <div class="fixed-bottom container p-3 bg-white border-top" style="max-width: 600px;">
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                {{ mb_strtoupper(__('messages.save_changes_btn')) }}
            </button>
        </div>
    </form>
</div>

<style>
    .form-section {
        background: #ffffff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #4b5563;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .footer-spacer {
        height: 100px;
    }

    /* Photo Upload Styles */
    .photo-upload-area {
        border: 2px dashed #0d6efd40;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background: #f8fafc;
    }

    .photo-upload-area:hover, .photo-upload-area:active {
        border-color: #0d6efd;
        background: #eff6ff;
    }

    .photo-preview-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .photo-thumb {
        position: relative;
        width: 76px;
        height: 76px;
    }

    .photo-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }

    .photo-thumb .remove-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ef4444;
        color: white;
        border: none;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photoFileInput');
    const previewGrid = document.getElementById('photoPreviewGrid');
    const hiddenContainer = document.getElementById('hiddenImagesContainer');
    const removeContainer = document.getElementById('removeImagesContainer');

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const thumb = document.createElement('div');
                    thumb.className = 'photo-thumb';

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const rmBtn = document.createElement('button');
                    rmBtn.type = 'button';
                    rmBtn.className = 'remove-btn';
                    rmBtn.innerHTML = '×';
                    rmBtn.onclick = () => {
                        thumb.remove();
                        newInput.remove();
                    };

                    thumb.appendChild(img);
                    thumb.appendChild(rmBtn);
                    previewGrid.appendChild(thumb);
                };
                reader.readAsDataURL(file);

                const newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.name = 'images[]';
                newInput.style.display = 'none';
                const dt = new DataTransfer();
                dt.items.add(file);
                newInput.files = dt.files;
                hiddenContainer.appendChild(newInput);
            });
            this.value = '';
        });
    }

    // Handle removing existing images
    document.querySelectorAll('.remove-existing-img').forEach(btn => {
        btn.addEventListener('click', function () {
            const imgPath = this.getAttribute('data-img');
            const thumb = this.closest('.current-image-thumb');
            
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'remove_images[]';
            hidden.value = imgPath;
            removeContainer.appendChild(hidden);

            thumb.remove();
        });
    });
});
</script>
@endsection