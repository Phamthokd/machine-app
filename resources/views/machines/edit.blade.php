@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Chỉnh sửa máy ' . $machine->ma_thiet_bi)

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('machines.index') }}" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h4 class="mb-0 fw-bold">Chỉnh sửa máy</h4>
                <div class="text-secondary small">Cập nhật dữ liệu thiết bị</div>
            </div>
        </div>

        @role('admin')
        <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ xoá TOÀN BỘ lịch sử sửa chữa và di chuyển của máy này. Bạn có chắc chắn muốn tiếp tục?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger border-0 d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                Xoá máy
            </button>
        </form>
        @endrole
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('machines.update', $machine->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-nowrap" style="min-width: 100%; font-size: 0.9rem;">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 px-2" style="min-width: 140px;">MÃ THIẾT BỊ</th>
                            <th class="py-3 px-2" style="min-width: 250px;">TÊN THIẾT BỊ</th>
                            <th class="py-3 px-2" style="min-width: 180px;">TỔ HIỆN TẠI</th>
                            <th class="py-3 px-2" style="min-width: 140px;">BRAND</th>
                            <th class="py-3 px-2" style="min-width: 140px;">MODEL</th>
                            <th class="py-3 px-2" style="min-width: 140px;">SERIAL</th>
                            <th class="py-3 px-2" style="min-width: 140px;">INVOICE/CD</th>
                            <th class="py-3 px-2" style="width: 80px;">YEAR</th>
                            <th class="py-3 px-2" style="min-width: 120px;">COUNTRY</th>
                            <th class="py-3 px-2" style="min-width: 140px;">STOCK-IN DATE</th>
                            <th class="py-3 px-2" style="min-width: 160px;">DEPARTMENT (TXT)</th>
                            <th class="py-3 px-2" style="min-width: 140px;">NGÀY VÀO KHO</th>
                            <th class="py-3 px-2" style="min-width: 140px;">NGÀY RA KHO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 bg-light">
                                <input type="text" class="form-control fw-bold text-primary bg-light border-0" value="{{ $machine->ma_thiet_bi }}" disabled readonly>
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="ten_thiet_bi" value="{{ old('ten_thiet_bi', $machine->ten_thiet_bi) }}" required>
                            </td>
                            <td class="p-2">
                                <select class="form-select border-0" name="current_department_id" required>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" @selected(old('current_department_id', $machine->current_department_id) == $d->id)>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="brand" value="{{ old('brand', $machine->brand) }}">
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="model" value="{{ old('model', $machine->model) }}">
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0 font-monospace text-secondary" name="serial" value="{{ old('serial', $machine->serial) }}">
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="invoice_cd" value="{{ old('invoice_cd', $machine->invoice_cd) }}">
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="year" value="{{ old('year', $machine->year) }}">
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="country" value="{{ old('country', $machine->country) }}">
                            </td>
                            <td class="p-2">
                                <input type="date" class="form-control border-0" name="stock_in_date" value="{{ old('stock_in_date', $machine->stock_in_date ? date('Y-m-d', strtotime($machine->stock_in_date)) : '') }}">
                            </td>
                            <td class="p-2">
                                <input type="text" class="form-control border-0" name="vi_tri_text" value="{{ old('vi_tri_text', $machine->vi_tri_text) }}">
                            </td>
                            <td class="p-2">
                                <input type="date" class="form-control border-0" name="ngay_vao_kho" value="{{ old('ngay_vao_kho', $machine->ngay_vao_kho ? date('Y-m-d', strtotime($machine->ngay_vao_kho)) : '') }}">
                            </td>
                            <td class="p-2">
                                <input type="date" class="form-control border-0" name="ngay_ra_kho" value="{{ old('ngay_ra_kho', $machine->ngay_ra_kho ? date('Y-m-d', strtotime($machine->ngay_ra_kho)) : '') }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                <a href="{{ route('machines.index') }}" class="btn btn-light me-3">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    /* Table Input Styling to make them feel like cells */
    .table td { padding: 0 !important; height: 50px; vertical-align: middle; }
    .table td input, .table td select {
        height: 100%;
        width: 100%;
        padding: 0 12px;
        border-radius: 0;
        background: transparent;
    }
    .table td input:focus, .table td select:focus {
        background: #fff;
        box-shadow: inset 0 0 0 2px var(--bs-primary);
        z-index: 10;
        position: relative;
    }
    .table td:hover { background-color: #f8fafc; }
</style>
@endsection
