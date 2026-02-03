@php
    $maxWidth = '1200px';
@endphp
@extends('layouts.app-simple')
@section('title', 'Import Danh Sách Máy')

@section('content')
<div class="row g-4">
    <!-- Left Column: Upload Form -->
    <div class="col-lg-7">
        
        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="/dashboard" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h4 class="mb-0 fw-bold">Import Danh Sách Máy</h4>
                <p class="text-secondary small mb-0">Tải lên dữ liệu máy móc từ file CSV hoặc Excel</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 shadow-sm border-0 rounded-3 mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Upload -->
        <div class="card border-0 shadow rounded-4 mb-4 h-100">
            <div class="card-body p-5">
                <form action="/machines/import-csv" method="POST" enctype="multipart/form-data" class="h-100 d-flex flex-column justify-content-center">
                    @csrf
                    
                    <div class="text-center mb-5">
                        <div class="avatar rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Tải lên file dữ liệu</h4>
                        <p class="text-secondary mb-0">Chọn file .csv từ máy tính của bạn để tiến hành nhập liệu</p>
                    </div>

                    <div class="mb-4">
                        <div class="border-2 border-dashed border-secondary border-opacity-25 rounded-4 p-5 text-center hover-bg-light transition cursor-pointer position-relative">
                            <input class="form-control form-control-lg position-absolute top-0 start-0 h-100 w-100 opacity-0 cursor-pointer" type="file" name="file" accept=".csv, .txt">
                            <div class="pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary mb-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <div class="fw-bold text-dark">Nhấn để chọn file</div>
                                <div class="small text-secondary">hoặc kéo thả file vào đây</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-lg tap text-uppercase letter-spacing-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Tiến hành Import
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Instructions -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Hướng dẫn Chuẩn bị File
                </h5>
            </div>
            <div class="card-body p-4">
                <p class="text-secondary mb-4">Để đảm bảo dữ liệu được nhập chính xác, vui lòng tuân thủ các quy tắc định dạng dưới đây.</p>
                
                <div class="alert alert-info border-0 d-flex gap-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-1"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <div>
                        <strong>Lưu ý về Font chữ:</strong>
                        <div class="small mt-1">File CSV cần được lưu với bảng mã <strong>UTF-8</strong> để không bị lỗi font Tiếng Việt.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3">Cấu trúc các cột bắt buộc</h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-3 py-2 text-secondary small text-uppercase">Tên cột (Header)</th>
                                    <th class="border-0 px-3 py-2 text-secondary small text-uppercase">Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-3 py-3 fw-bold text-primary font-monospace">ma_thiet_bi</td>
                                    <td class="px-3 py-3 text-secondary">Mã định danh duy nhất (VD: MA-001)</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 fw-bold text-primary font-monospace">ten_thiet_bi</td>
                                    <td class="px-3 py-3 text-secondary">Tên loại máy (VD: Máy vắt sổ)</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 fw-bold text-primary font-monospace">department</td>
                                    <td class="px-3 py-3 text-secondary">Tên tổ / Vị trí (VD: Tổ 1)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-outline-primary py-2 fw-semibold border-2 d-flex align-items-center justify-content-center gap-2" onclick="alert('Chức năng tải mẫu đang được cập nhật...');">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Tải file mẫu chuẩn (.csv)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
