@extends('layouts.app-simple')
@section('title','Tạo phiếu sửa - ' . $machine->ma_thiet_bi)

@section('content')
<style>
    :root {
        --primary-color: #4f46e5;
        --bg-app: #f8fafc;
    }
    body {
        background-color: var(--bg-app) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    /* Machine Summary Card */
    .machine-summary {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .machine-summary .label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }
    .machine-summary .value {
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Form Styles */
    .form-section {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .form-label {
        font-weight: 500;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 16px;
        border-color: #e2e8f0;
        background-color: #fff;
        font-size: 1rem; /* Prevent zoom on iOS */
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    textarea.form-control {
        min-height: 100px;
    }

    /* Sticky Bottom Bar */
    .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 16px; /* Sẽ được padding css cũ cover, nhưng mình custom lại cho chắc */
        border-top: 1px solid #e2e8f0;
        z-index: 100;
        padding-bottom: max(16px, env(safe-area-inset-bottom));
        box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.05);
    }
    .btn-submit {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 1rem;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-submit:active {
        transform: scale(0.98);
    }
    
    /* Spacer for sticky footer */
    .footer-spacer {
        height: 100px;
    }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/m/{{ $machine->ma_thiet_bi }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">Tạo phiếu sửa</h4>
</div>

<form method="POST" action="/repairs">
    @csrf
    <input type="hidden" name="machine_id" value="{{ $machine->id }}">
    <input type="hidden" name="department_id" value="{{ $machine->department->id }}">

    <!-- Machine Info -->
    <div class="machine-summary">
        <div class="row g-3">
            <div class="col-6">
                <div class="label">Mã thiết bị</div>
                <div class="value">{{ $machine->ma_thiet_bi }}</div>
            </div>
            <div class="col-6">
                <div class="label">Tổ</div>
                <div class="value">{{ $machine->department->name }}</div>
            </div>
            <div class="col-12">
                <div class="label">Tên thiết bị</div>
                <div class="value">{{ $machine->ten_thiet_bi }}</div>
            </div>
        </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
        <div class="fw-bold mb-2">⚠️ Vui lòng kiểm tra lại:</div>
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Issue Details -->
    <div class="form-section">
        <div class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Thông tin sửa chữa
        </div>
        
        @hasrole('contractor')
            <!-- Simplified Form for Contractor -->
            <div class="mb-3">
                <label class="form-label">Nguyên nhân hư hỏng <span class="text-danger">*</span></label>
                <textarea class="form-control" name="nguyen_nhan" placeholder="VD: Đứt chỉ, kẹt ổ, gãy kim..." required>{{ old('nguyen_nhan') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Nội dung sửa chữa <span class="text-danger">*</span></label>
                <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Thay kim, chỉnh ổ, vệ sinh..." required>{{ old('noi_dung_sua_chua') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Người hỗ trợ (nếu có)</label>
                <select class="form-select" name="nguoi_ho_tro">
                    <option value="">-- Chọn người hỗ trợ --</option>
                    @foreach($contractors as $c)
                        <option value="{{ $c->name }}" @selected(old('nguoi_ho_tro') == $c->name)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Hidden fields for Contractor -->
            <input type="hidden" name="ma_hang" value="N/A">
            <input type="hidden" name="cong_doan" value="N/A">
            <input type="hidden" name="endline_qc_name" value="N/A">
        
        @else
            @hasrole('team_leader')
                <!-- Type Selection -->
                <div class="mb-3">
                    <label class="form-label">Loại yêu cầu <span class="text-danger">*</span></label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_mechanic" value="mechanic" checked>
                            <label class="form-check-label" for="type_mechanic">
                                🔧 Sửa máy
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_contractor" value="contractor">
                            <label class="form-check-label" for="type_contractor">
                                🏗 Công trình
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Simplified Form for Team Leader (Request Only) -->
                <div class="mb-3">
                    <label class="form-label">Mô tả sự cố / Hư hỏng <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="nguyen_nhan" placeholder="VD: Máy kêu to, không chạy, đứt chỉ..." rows="4" required>{{ old('nguyen_nhan') }}</textarea>
                </div>

                <!-- Hidden fields for Team Leader -->
                <input type="hidden" name="ma_hang" value="N/A">
                <input type="hidden" name="cong_doan" value="N/A">
                <input type="hidden" name="noi_dung_sua_chua" value="N/A"> <!-- Will be updated later by mechanic -->
                <input type="hidden" name="endline_qc_name" value="N/A">
            @else
                <!-- Standard Form for Repair Tech / Admin -->
                <div class="mb-3">
                    <label class="form-label">Mã hàng <span class="text-danger">*</span></label>
                    <input class="form-control" name="ma_hang" value="{{ old('ma_hang') }}" placeholder="VD: H1-12345" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Công đoạn <span class="text-danger">*</span></label>
                    <input class="form-control" name="cong_doan" value="{{ old('cong_doan') }}" placeholder="VD: Tra gấu" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nguyên nhân hư hỏng <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="nguyen_nhan" placeholder="VD: Đứt chỉ, kẹt ổ, gãy kim..." required>{{ old('nguyen_nhan') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung khắc phục <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="noi_dung_sua_chua" placeholder="VD: Thay kim, chỉnh ổ, vệ sinh..." required>{{ old('noi_dung_sua_chua') }}</textarea>
                </div>
            @endhasrole
        @endhasrole
    </div>

    @unlessrole('contractor|team_leader')
    <!-- Time & Personnel (Only for Non-Contractors & Non-TeamLeaders) -->
    <div class="form-section">
        <div class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Thời gian & Nhân sự
        </div>

        <div class="mb-3">
            <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
            <input
              class="form-control"
              type="datetime-local"
              name="started_at"
              value="{{ old('started_at', now(config('app.timezone'))->format('Y-m-d\\TH:i')) }}"
              required
              data-auto-now>
        </div>

        <!-- Static QC Fields -->
        <div class="mb-3">
            <label class="form-label">Tổ trưởng Endline QC <span class="text-danger">*</span></label>
            <select class="form-select" name="endline_qc_name" required>
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

    </div>
    @endunlessrole

    @hasrole('team_leader')
        <!-- Hidden started_at for Team Leader (Auto Now) -->
        <input type="hidden" name="started_at" value="{{ now()->format('Y-m-d\\TH:i') }}">
    @endhasrole

    <!-- Spacer to ensure content isn't hidden behind footer -->
    <div class="footer-spacer"></div>

    <!-- Submit Button -->
    <div class="fixed-bottom container p-3 bg-white border-top" style="max-width: 600px;">
        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2">
            @hasrole('team_leader')
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                GỬI BÁO HỎNG
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                LƯU PHIẾU SỬA
            @endhasrole
        </button>
    </div>

</form>

<script>
  const startedAtField = document.querySelector('[data-auto-now]');
  if (startedAtField && !startedAtField.value) {
    const now = new Date();
    const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
      .toISOString()
      .slice(0, 16);
    startedAtField.value = local;
  }
</script>
@endsection
