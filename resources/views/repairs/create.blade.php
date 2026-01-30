@extends('layouts.app-simple')
@section('title','Tạo phiếu sửa')

@section('content')
<div class="card shadow-sm">
  <div class="card-body p-3">
    <div class="d-flex align-items-start justify-content-between gap-3">
      <div>
        <h5 class="mb-1">Tạo phiếu sửa máy</h5>
        <div class="text-muted small">Quét QR → tự điền Tổ/Mã/Tên. Nhập phần còn lại.</div>
      </div>
      <a class="btn btn-outline-secondary btn-sm tap" href="/m/{{ $machine->ma_thiet_bi }}">Quay lại</a>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger mt-3">
        <div class="fw-semibold mb-2">Vui lòng kiểm tra lại:</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form class="mt-3" method="POST" action="/repairs">
      @csrf
      <input type="hidden" name="machine_id" value="{{ $machine->id }}">
      <input type="hidden" name="department_id" value="{{ $machine->department->id }}">

      <div class="mb-3">
        <label class="form-label">Tổ</label>
        <input class="form-control" value="{{ $machine->department->name }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Mã thiết bị</label>
        <input class="form-control" value="{{ $machine->ma_thiet_bi }}" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Tên thiết bị</label>
        <input class="form-control" value="{{ $machine->ten_thiet_bi }}" readonly>
      </div>

      <hr>

      <div class="mb-3">
        <label class="form-label">Mã hàng</label>
        <input class="form-control" name="ma_hang" value="{{ old('ma_hang') }}" placeholder="VD: H1-12345" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Công đoạn</label>
        <input class="form-control" name="cong_doan" value="{{ old('cong_doan') }}" placeholder="VD: Tra gấu" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nguyên nhân</label>
        <textarea class="form-control" name="nguyen_nhan" rows="3" placeholder="VD: Đứt chỉ, kẹt ổ..." required>{{ old('nguyen_nhan') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Nội dung sửa chữa</label>
        <textarea class="form-control" name="noi_dung_sua_chua" rows="3" placeholder="VD: Thay kim, chỉnh ổ..." required>{{ old('noi_dung_sua_chua') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Giờ bắt đầu</label>
        <input class="form-control" type="datetime-local" name="started_at" value="{{ old('started_at') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Giờ kết thúc</label>
        <input class="form-control" type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" required>
      </div>

      <hr>

      <div class="mb-3">
        <label class="form-label">Endline QC</label>
        <select class="form-select" name="endline_qc_user_id" required>
          <option value="">-- Chọn Endline QC --</option>
          @foreach($endlineQcs as $u)
            <option value="{{ $u->id }}" @selected(old('endline_qc_user_id')==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Inline QC (Triumph)</label>
        <select class="form-select" name="inline_qc_user_id" required>
          <option value="">-- Chọn Inline QC --</option>
          @foreach($inlineQcs as $u)
            <option value="{{ $u->id }}" @selected(old('inline_qc_user_id')==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Chủ quản QA | QA主管 (Triumph)</label>
        <select class="form-select" name="qa_supervisor_user_id" required>
          <option value="">-- Chọn QA主管 --</option>
          @foreach($qaSupers as $u)
            <option value="{{ $u->id }}" @selected(old('qa_supervisor_user_id')==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Sticky actions: dễ bấm trên điện thoại -->
      <div class="sticky-actions">
        <div class="d-grid gap-2">
          <button class="btn btn-primary btn-lg tap" type="submit">Lưu phiếu sửa</button>
          <a class="btn btn-outline-secondary tap" href="/m/{{ $machine->ma_thiet_bi }}">Hủy / Quay lại</a>
        </div>
      </div>

    </form>
  </div>
</div>
@endsection
