@extends('layouts.app-simple')
@section('title', 'Tạo phiếu IT')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ $machine ? '/m/' . $machine->ma_thiet_bi : route('it-repairs.index') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">🖥️ Báo sự cố IT</h4>
</div>

@if($machine ?? false)
<div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#4f46e5,#6366f1);color:white;">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
    <div>
        <div class="fw-bold">{{ $machine->ma_thiet_bi }}</div>
        <div class="opacity-85 small">{{ $machine->ten_thiet_bi }} &middot; {{ $machine->department->name ?? '—' }}</div>
    </div>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 rounded-3 mb-4">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4" style="max-width:760px; margin:0 auto;">
    <div class="card-body p-4">
        <form action="{{ route('it-repairs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($machine ?? false)
            <input type="hidden" name="machine_id" value="{{ $machine->id }}">
            @endif

            {{-- Loại sự cố --}}
            <div class="mb-4">
                <label class="form-label fw-bold">Loại sự cố <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach([
                        'computer' => ['💻', 'Máy tính'],
                        'network'  => ['🌐', 'Mạng / Internet'],
                        'printer'  => ['🖨️', 'Máy in'],
                        'software' => ['⚙️', 'Phần mềm'],
                        'other'    => ['❓', 'Khác'],
                    ] as $val => [$icon, $label])
                    <div>
                        <input type="radio" class="btn-check" id="type_{{ $val }}" name="issue_type" value="{{ $val }}" required
                               @checked(old('issue_type') === $val)>
                        <label class="btn btn-outline-secondary rounded-3 px-3 py-2" for="type_{{ $val }}">
                            {{ $icon }} {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Mức ưu tiên --}}
            <div class="mb-4">
                <label class="form-label fw-bold">Mức độ ưu tiên <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach([
                        'low'    => ['⚪', 'Thấp',       'outline-secondary'],
                        'medium' => ['🔵', 'Bình thường', 'outline-info'],
                        'high'   => ['🟠', 'Cao',         'outline-warning'],
                        'urgent' => ['🔴', 'Khẩn cấp',   'outline-danger'],
                    ] as $val => [$icon, $label, $cls])
                    <div>
                        <input type="radio" class="btn-check" id="priority_{{ $val }}" name="priority" value="{{ $val }}" required
                               @checked(old('priority', 'medium') === $val)>
                        <label class="btn btn-{{ $cls }} rounded-3 px-3 py-2" for="priority_{{ $val }}">
                            {{ $icon }} {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tiêu đề --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề sự cố <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control rounded-3"
                       value="{{ old('title') }}"
                       placeholder="VD: Máy tính không lên nguồn, Mạng bị chậm..."
                       required maxlength="255">
            </div>

            {{-- Mô tả --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control rounded-3" rows="4"
                          placeholder="Mô tả cụ thể sự cố, triệu chứng, thời điểm xảy ra..."
                          required>{{ old('description') }}</textarea>
            </div>

            {{-- Vị trí --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Vị trí / Phòng</label>
                <input type="text" name="location" class="form-control rounded-3"
                       value="{{ old('location') }}"
                       placeholder="VD: Tầng 3 – Phòng kế toán, Xưởng 6 Tầng 1...">
            </div>

            {{-- Ảnh đính kèm --}}
            <div class="mb-4">
                <label class="form-label fw-bold">Ảnh đính kèm <span class="text-muted fw-normal small">(tùy chọn)</span></label>
                <div id="photoPreviewArea" class="d-flex flex-wrap gap-2 mb-2"></div>
                <button type="button" id="btnAddPhoto" class="btn btn-outline-secondary rounded-3 d-flex align-items-center gap-2 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Thêm ảnh
                </button>
                <div id="hiddenImagesContainer"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm fs-6">
                📤 Gửi phiếu IT
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('btnAddPhoto').addEventListener('click', function () {
    const input = document.createElement('input');
    input.type = 'file';
    input.name = 'images[]';
    input.accept = 'image/*';
    input.multiple = true;
    input.style.display = 'none';
    input.addEventListener('change', function () {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const wrap = document.createElement('div');
                wrap.className = 'position-relative';
                wrap.style.cssText = 'width:80px;height:80px;';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail w-100 h-100 object-fit-cover rounded-3';
                const rm = document.createElement('button');
                rm.type = 'button';
                rm.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center';
                rm.style.cssText = 'width:20px;height:20px;transform:translate(40%,-40%);font-size:11px;';
                rm.innerHTML = '×';
                rm.onclick = () => { wrap.remove(); input.remove(); };
                wrap.appendChild(img);
                wrap.appendChild(rm);
                document.getElementById('photoPreviewArea').appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
        document.getElementById('hiddenImagesContainer').appendChild(input);
    });
    input.click();
});
</script>
@endsection
