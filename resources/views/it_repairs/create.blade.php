@extends('layouts.app-simple')
@section('title', 'Tạo phiếu IT')

@section('content')
<style>
    :root {
        --it-primary: #4f46e5;
        --it-primary-light: #eef2ff;
        --bg-app: #f8fafc;
    }

    body {
        background-color: var(--bg-app) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .machine-summary {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .machine-summary .label {
        color: #a5b4fc;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 3px;
    }

    .machine-summary .value {
        font-weight: 700;
        font-size: 1rem;
    }

    .form-section {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 14px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        border: 1px solid #f1f5f9;
    }

    .section-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 7px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .section-title .icon-box {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--it-primary-light);
        color: var(--it-primary);
        flex-shrink: 0;
    }

    .form-label {
        font-weight: 600;
        color: #334155;
        font-size: 0.88rem;
        margin-bottom: 7px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 11px 14px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #fafafa;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--it-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        background: white;
    }

    textarea.form-control { min-height: 90px; resize: vertical; }

    /* Issue Type Chips */
    .type-chip-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .type-chip input[type="radio"] { display: none; }

    .type-chip label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 12px 6px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        background: #fafafa;
        cursor: pointer;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
        text-align: center;
        min-height: 68px;
    }

    .type-chip label .chip-icon { font-size: 1.5rem; line-height: 1; }

    .type-chip input[type="radio"]:checked + label {
        border-color: var(--it-primary);
        background: var(--it-primary-light);
        color: var(--it-primary);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
    }

    /* Time row */
    .time-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* Photo Upload */
    .photo-upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 18px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
    }

    .photo-upload-area:hover { border-color: var(--it-primary); background: var(--it-primary-light); }

    .photo-preview-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }

    .photo-thumb { position: relative; width: 76px; height: 76px; }

    .photo-thumb img {
        width: 100%; height: 100%;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
    }

    .photo-thumb .remove-btn {
        position: absolute; top: -6px; right: -6px;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: #ef4444;
        color: white; border: none;
        font-size: 12px; display: flex;
        align-items: center; justify-content: center;
        cursor: pointer; line-height: 1;
    }

    /* Resolve hint */
    .resolve-hint {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1.5px solid #86efac;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.83rem;
        color: #166534;
        font-weight: 500;
    }

    /* Sticky footer */
    .sticky-footer {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: white;
        padding: 14px 16px;
        border-top: 1px solid #e2e8f0;
        z-index: 100;
        padding-bottom: max(14px, env(safe-area-inset-bottom));
        box-shadow: 0 -4px 12px rgba(0,0,0,0.07);
    }

    .btn-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: white; border: none;
        border-radius: 12px; padding: 14px;
        font-weight: 700; font-size: 1rem; width: 100%;
        display: flex; align-items: center; justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(79,70,229,0.35);
        transition: all 0.2s;
    }

    .btn-submit:active { transform: scale(0.98); opacity: 0.9; }

    .footer-spacer { height: 90px; }
</style>

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ $machine ? '/m/' . $machine->ma_thiet_bi : route('it-repairs.index') }}"
       class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">🖥️ Phiếu sửa chữa IT</h4>
</div>

@if($errors->any())
<div class="alert alert-danger border-0 rounded-3 mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Hint --}}
<div class="resolve-hint">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    Bộ phận IT điền đầy đủ thông tin sự cố và nội dung xử lý — phiếu sẽ được ghi nhận là <strong class="ms-1">Đã giải quyết</strong>.
</div>

<form action="{{ route('it-repairs.store') }}" method="POST" enctype="multipart/form-data" id="itRepairForm">
    @csrf
    @if($machine ?? false)
    <input type="hidden" name="machine_id" value="{{ $machine->id }}">
    @endif

    {{-- Machine card --}}
    @if($machine ?? false)
    <div class="machine-summary">
        <div class="row g-3">
            <div class="col-5">
                <div class="label">Mã thiết bị</div>
                <div class="value">{{ $machine->ma_thiet_bi }}</div>
            </div>
            <div class="col-7">
                <div class="label">Bộ phận</div>
                <div class="value">{{ $machine->department->name ?? '—' }}</div>
            </div>
            <div class="col-12">
                <div class="label">Tên thiết bị</div>
                <div class="value" style="font-size:0.95rem;">{{ $machine->ten_thiet_bi }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- SECTION 1: Loại sự cố --}}
    <div class="form-section">
        <div class="section-title">
            <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            Loại sự cố <span class="text-danger ms-1">*</span>
        </div>
        <div class="type-chip-grid">
            @foreach([
                'computer' => ['💻', 'Máy tính'],
                'network'  => ['🌐', 'Mạng / Internet'],
                'printer'  => ['🖨️', 'Máy in'],
                'software' => ['⚙️', 'Phần mềm'],
                'phone'    => ['📱', 'Điện thoại'],
                'other'    => ['🔧', 'Khác'],
            ] as $val => [$icon, $label])
            <div class="type-chip">
                <input type="radio" id="type_{{ $val }}" name="issue_type" value="{{ $val }}" required
                       @checked(old('issue_type') === $val)>
                <label for="type_{{ $val }}">
                    <span class="chip-icon">{{ $icon }}</span>
                    {{ $label }}
                </label>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION 2: Nội dung sự cố & Xử lý --}}
    <div class="form-section">
        <div class="section-title">
            <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            Nội dung sự cố & Xử lý
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control"
                      placeholder="Mô tả triệu chứng, thời điểm xảy ra, thiết bị nào bị ảnh hưởng..."
                      required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-0">
            <label class="form-label" style="color:#166534;">
                🔧 Nội dung xử lý / Khắc phục <span class="text-danger">*</span>
            </label>
            <textarea name="resolution_note" class="form-control"
                      style="border-color:#86efac; background:#f0fdf4;"
                      placeholder="VD: Thay cáp mạng, cài lại driver, reset thiết bị, cài đặt phần mềm..."
                      required>{{ old('resolution_note') }}</textarea>
        </div>
    </div>

    {{-- SECTION 3: Thời gian xử lý --}}
    <div class="form-section">
        <div class="section-title">
            <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            Thời gian xử lý
        </div>
        <div class="time-row">
            <div>
                <label class="form-label">Bắt đầu</label>
                <input type="datetime-local" name="started_at" class="form-control"
                       value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}">
            </div>
            <div>
                <label class="form-label">Kết thúc</label>
                <input type="datetime-local" name="ended_at" class="form-control"
                       value="{{ old('ended_at', now()->format('Y-m-d\TH:i')) }}">
            </div>
        </div>
    </div>

    {{-- SECTION 4: Ảnh đính kèm --}}
    <div class="form-section">
        <div class="section-title">
            <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
            Ảnh đính kèm <span class="text-muted fw-normal ms-1" style="font-size:0.78rem;text-transform:none;">(tùy chọn)</span>
        </div>

        <div class="photo-upload-area" onclick="document.getElementById('photoFileInput').click()">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" class="mb-2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <div style="color:#64748b;font-size:0.85rem;font-weight:600;">Chạm để thêm ảnh</div>
            <div style="color:#94a3b8;font-size:0.75rem;margin-top:3px;">Hỗ trợ JPG, PNG, HEIC</div>
        </div>
        <input type="file" id="photoFileInput" name="images[]" accept="image/*" multiple style="display:none;">
        <div class="photo-preview-grid" id="photoPreviewGrid"></div>
        <div id="hiddenImagesContainer"></div>
    </div>

    <div class="footer-spacer"></div>
</form>

<div class="sticky-footer">
    <button type="submit" form="itRepairForm" class="btn-submit">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Ghi nhận phiếu IT
    </button>
</div>

<script>
const photoInput = document.getElementById('photoFileInput');
const previewGrid = document.getElementById('photoPreviewGrid');
const hiddenContainer = document.getElementById('hiddenImagesContainer');

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
            rmBtn.onclick = () => { thumb.remove(); newInput.remove(); };

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
</script>
@endsection
