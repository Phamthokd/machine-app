@extends('layouts.app-simple')
@section('title', 'Hoàn thành phiếu sửa')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="/repair-requests" class="text-decoration-none text-secondary d-flex align-items-center gap-1 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Quay lại
        </a>
        <h3 class="fw-bold mb-1">Tiếp nhận sửa chữa</h3>
        <p class="text-secondary mb-0">Hoàn thiện thông tin cho phiếu báo hỏng #{{ $repair->code }}</p>
    </div>

    <!-- Machine Info Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">Mã thiết bị</div>
                    <div class="h3 fw-bold mb-1">{{ $machine->ma_thiet_bi }}</div>
                    <div class="fw-medium text-white-50">{{ $machine->ten_thiet_bi }}</div>
                </div>
                <div class="text-end">
                    <div class="text-white-50 text-uppercase fw-bold text-xs mb-1">Tổ</div>
                    <div class="h5 fw-bold mb-0">{{ $machine->department->name }}</div>
                </div>
            </div>
        </div>
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

    <form action="/repairs/{{ $repair->id }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Issue Details -->
        <div class="form-section">
            <div class="section-title">
                Thông tin sự cố
            </div>
            
            @if($repair->type == 'contractor')
                <!-- CONTRACTOR FORM -->
                <div class="mb-3">
                    <label class="form-label">Nguyên nhân hư hỏng <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="nguyen_nhan" required>{{ old('nguyen_nhan', $repair->nguyen_nhan) }}</textarea>
                    <div class="form-text">Mô tả chi tiết nguyên nhân sự cố công trình.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung khắc phục <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Thay bóng đèn, sửa ống nước..." required>{{ old('noi_dung_sua_chua') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Người hỗ trợ (nếu có)</label>
                    <select class="form-select" name="nguoi_ho_tro">
                        <option value="">-- Chọn người hỗ trợ --</option>
                        @foreach($contractors as $c)
                            <option value="{{ $c->name }}" @selected(old('nguoi_ho_tro', $repair->nguoi_ho_tro) == $c->name)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- MECHANIC FORM -->
                <div class="mb-3">
                    <label class="form-label">Mã hàng <span class="text-danger">*</span></label>
                    <input class="form-control" name="ma_hang" value="{{ old('ma_hang', $repair->ma_hang == 'N/A' ? '' : $repair->ma_hang) }}" placeholder="VD: H1-12345" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Công đoạn <span class="text-danger">*</span></label>
                    <input class="form-control" name="cong_doan" value="{{ old('cong_doan', $repair->cong_doan == 'N/A' ? '' : $repair->cong_doan) }}" placeholder="VD: Tra gấu" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nguyên nhân hư hỏng <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="nguyen_nhan" required>{{ old('nguyen_nhan', $repair->nguyen_nhan) }}</textarea>
                    <div class="form-text">Nguyên nhân ban đầu do tổ trưởng báo.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung khắc phục <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Thay kim, chỉnh ổ, vệ sinh..." required>{{ old('noi_dung_sua_chua') }}</textarea>
                </div>
            @endif
        </div>

        <!-- Time & Personnel -->
        <div class="form-section">
            <div class="section-title">
                Thời gian & Nhân sự
            </div>

            <div class="mb-3">
                <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                <input
                class="form-control"
                type="datetime-local"
                name="started_at"
                value="{{ old('started_at', now()->format('Y-m-d\\TH:i')) }}"
                required>
                <div class="form-text">Thời gian bắt đầu được tính từ lúc bạn tiếp nhận (bây giờ).</div>
            </div>

            @if($repair->type != 'contractor')
                <!-- Static QC Fields (Only for Mechanics) -->
                <div class="mb-3">
                    <label class="form-label">Tổ trưởng Endline QC </label>
                    <span class="badge bg-light text-secondary fw-normal">Không bắt buộc</span>
                    <select class="form-select" name="endline_qc_name">
                        <option value="">-- Chọn Endline QC --</option>
                        <option value="Ánh" @selected(old('endline_qc_name') == 'Ánh')>Ánh</option>
                        <option value="Thuỷ" @selected(old('endline_qc_name') == 'Thuỷ')>Thuỷ</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Inline QC (Triumph)</label>
                        <span class="badge bg-light text-secondary fw-normal">Không bắt buộc</span>
                    </div>
                    <select class="form-select" name="inline_qc_name">
                        <option value="">-- Chọn Inline QC --</option>
                        <option value="Mai" @selected(old('inline_qc_name') == 'Mai')>Mai</option>
                        <option value="Liên" @selected(old('inline_qc_name') == 'Liên')>Liên</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Chủ quản QA (Triumph)</label>
                        <span class="badge bg-light text-secondary fw-normal">Không bắt buộc</span>
                    </div>
                    <select class="form-select" name="qa_supervisor_name">
                    <option value="" selected>-- Chọn QA --</option>
                        <option value="Tracy" >Tracy</option>
                    </select>
                </div>
            @endif
        </div>

        <!-- Spacer -->
        <div class="footer-spacer"></div>

        <!-- Submit Button -->
        <div class="fixed-bottom container p-3 bg-white border-top" style="max-width: 600px;">
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                HOÀN THÀNH PHIẾU
            </button>
        </div>
    </form>
</div>

<style>
.form-section {
    background: #ffffff;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
</style>
@endsection
