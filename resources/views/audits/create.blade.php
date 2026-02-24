@extends('layouts.app-simple')
@section('title', 'Thực hiện đánh giá')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="/audits" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Quay lại
        </a>
        <h3 class="fw-bold mb-1">{{ $template->name }}</h3>
        <p class="text-secondary mb-0">Bộ phận đánh giá: <span class="badge bg-primary">{{ $template->department_name }}</span></p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div class="fw-bold">Vui lòng kiểm tra lại:</div>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/audits" method="POST" id="auditForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="audit_template_id" value="{{ $template->id }}">

        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-0">
                @forelse($template->criteria as $index => $criterion)
                    <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="mb-3">
                            <h5 class="fw-bold text-dark lh-base">
                                <span class="bg-light text-secondary rounded px-2 py-1 me-2 fs-6">{{ $index + 1 }}</span>
                                {{ $criterion->content }}
                            </h5>
                        </div>

                        <!-- Checkbox Logic -->
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <!-- Input hidden để lưu criterion id và giá trị pass -->
                            <input type="hidden" name="results[{{ $index }}][audit_criterion_id]" value="{{ $criterion->id }}">
                            
                            <!-- Nút Đạt (V) -->
                            <label class="audit-btn btn btn-outline-success d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-3" style="max-width: 200px; border-radius: 12px;">
                                <input type="radio" class="btn-check audit-radio-pass" name="results[{{ $index }}][is_passed]" value="1" autocomplete="off" 
                                    @if(old("results.{$index}.is_passed") === "1") checked @endif required>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span class="fw-bold fs-5">ĐẠT (V)</span>
                            </label>

                            <!-- Nút Không Đạt (X) -->
                            <label class="audit-btn btn btn-outline-danger d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-3" style="max-width: 200px; border-radius: 12px;">
                                <input type="radio" class="btn-check audit-radio-fail" name="results[{{ $index }}][is_passed]" value="0" autocomplete="off"
                                    @if(old("results.{$index}.is_passed") === "0") checked @endif required>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span class="fw-bold fs-5">LỖI (X)</span>
                            </label>
                        </div>

                        <!-- Khu vực nhập ghi chú và ảnh (Chỉ hiện khi chọn Không Đạt) -->
                        <div class="note-container" style="display: {{ old("results.{$index}.is_passed") === "0" ? 'block' : 'none' }};">
                            <!-- Ghi chú lỗi -->
                            <div class="mb-3">
                                <label class="form-label text-danger fw-semibold d-flex align-items-center gap-1 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    Ghi chú lỗi (Bắt buộc)
                                </label>
                                <textarea class="form-control bg-light" name="results[{{ $index }}][note]" rows="2" placeholder="Nhập chi tiết về tình trạng phát hiện được...">{{ old("results.{$index}.note") }}</textarea>
                            </div>

                            <!-- Ảnh đính kèm (Option) -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold text-secondary d-flex align-items-center gap-1 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    Ảnh đính kèm lỗi (Tuỳ chọn)
                                </label>
                                <div class="position-relative">
                                    <input type="file" class="form-control bg-light file-input" name="results[{{ $index }}][image]" accept="image/*" capture="environment">
                                </div>
                                <div class="form-text mt-1"><small>Bấm để chọn file hoặc chụp ảnh trực tiếp trên điện thoại.</small></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        Bộ đánh giá này chưa có câu hỏi nào được định nghĩa.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Footer Spacer -->
        <div style="height: 100px;"></div>

        <!-- Submit Button -->
        <div class="fixed-bottom container p-3 bg-white border-top shadow-lg" style="max-width: 600px;">
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 text-uppercase d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Lưu kết quả đánh giá
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioPassBtns = document.querySelectorAll('.audit-radio-pass');
    const radioFailBtns = document.querySelectorAll('.audit-radio-fail');

    // Hiện ẩn textarea note khi bấm Fail/Pass
    function handleRadioChange(e) {
        const isFail = e.target.classList.contains('audit-radio-fail');
        const container = e.target.closest('.p-4'); // Container bao toàn bộ 1 câu hỏi
        const noteContainer = container.querySelector('.note-container');
        const textarea = noteContainer.querySelector('textarea');
        
        if (isFail) {
            noteContainer.style.display = 'block';
            textarea.setAttribute('required', 'required');
            textarea.focus();
        } else {
            noteContainer.style.display = 'none';
            textarea.removeAttribute('required');
        }
    }

    radioPassBtns.forEach(btn => btn.addEventListener('change', handleRadioChange));
    radioFailBtns.forEach(btn => btn.addEventListener('change', handleRadioChange));
});
</script>

<style>
/* Tùy chỉnh CSS cho nút Chọn */
.btn-check:checked + .audit-btn {
    opacity: 1;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.btn-check:not(:checked) + .audit-btn {
    opacity: 0.6;
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #64748b;
}
.btn-check:not(:checked) + .btn-outline-success:hover {
    color: #198754;
    border-color: #198754;
    opacity: 0.8;
}
.btn-check:not(:checked) + .btn-outline-danger:hover {
    color: #dc3545;
    border-color: #dc3545;
    opacity: 0.8;
}
.audit-btn {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
@endsection
