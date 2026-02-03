@extends('layouts.app-simple')
@section('title', 'Chuyển tổ - ' . $machine->ma_thiet_bi)

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/m/{{ $machine->ma_thiet_bi }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">Chuyển máy sang tổ khác</h4>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        
        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <div class="fw-bold fs-5">{{ $machine->ma_thiet_bi }}</div>
                <div class="text-muted small">{{ $machine->ten_thiet_bi }}</div>
            </div>
        </div>

        <form method="POST" action="/machines/{{ $machine->id }}/move">
            @csrf
            
            <div class="mb-4">
                <label class="form-label fw-medium text-secondary">Tổ hiện tại</label>
                <div class="form-control bg-light border-0 text-secondary">
                    {{ $machine->department->name }}
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Chọn tổ mới <span class="text-danger">*</span></label>
                <select class="form-select" name="department_id" required>
                    <option value="">-- Chọn tổ đến --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" @disabled($d->id == $machine->department_id)>
                            {{ $d->name }} 
                            @if($d->id == $machine->department_id) (Hiện tại) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Ghi chú vị trí (Optional)</label>
                <textarea class="form-control" name="note" rows="3" placeholder="Ví dụ: Chuyển cho chuyền 2 mượn...">{{ $machine->vi_tri_text }}</textarea>
                <div class="form-text">Ghi chú này sẽ cập nhật vào trường "Vị trí" của máy.</div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm tap">
                XÁC NHẬN CHUYỂN
            </button>
        </form>
    </div>
</div>
@endsection
