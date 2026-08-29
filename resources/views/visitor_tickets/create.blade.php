@php
    $maxWidth = '100%';
@endphp
@extends('layouts.app-simple')
@section('title', 'Tạo Phiếu Đăng Ký Khách')

@section('content')

<style>
    .guest-input-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fafafa;
    }
    @media (max-width: 768px) {
        .form-control {
            font-size: 15px !important;
            min-height: 44px;
        }
    }
</style>

<div class="d-flex align-items-center gap-2 mb-3 mb-md-4">
    <a href="{{ route('visitor-tickets.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 d-flex align-items-center gap-1 text-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="d-none d-sm-inline">Quay lại</span>
    </a>
    <h4 class="mb-0 fw-bold fs-5 fs-md-4">Tạo Phiếu Đăng Ký Khách</h4>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12">
        <form method="POST" action="{{ route('visitor-tickets.store') }}" id="ticketForm">
            @csrf

            {{-- 1. Thông tin phiếu --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                <div class="card-header bg-transparent border-bottom px-4 pt-3 pt-md-4 pb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                        <span class="badge rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.75rem;">1</span>
                        Thông Tin Phiếu
                    </h6>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-secondary">Đơn vị khách <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('guest_unit') is-invalid @enderror"
                                   name="guest_unit" value="{{ old('guest_unit') }}"
                                   placeholder="Tên công ty / đơn vị của khách..." required>
                            @error('guest_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-secondary">Ngày đến <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('visit_date') is-invalid @enderror"
                                   name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required>
                            @error('visit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-secondary">Mục đích vào công ty <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('purpose') is-invalid @enderror"
                                   name="purpose" value="{{ old('purpose') }}"
                                   placeholder="VD: Trao đổi công việc, sửa chữa thiết bị, giao hàng..." required>
                            @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Danh sách khách --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom px-4 pt-3 pt-md-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2 text-dark">
                        <span class="badge rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.75rem;">2</span>
                        Danh Sách Khách
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-flex align-items-center gap-1 shadow-sm fw-semibold" id="addGuestBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>+ Thêm khách</span>
                    </button>
                </div>

                @error('guests')
                <div class="alert alert-danger mx-3 mt-3 py-2 small">{{ $message }}</div>
                @enderror

                <div class="card-body p-3 p-md-4">
                    {{-- Container chứa các dòng khách (responsive card list) --}}
                    <div id="guestCardsContainer" class="d-flex flex-column gap-3">
                        {{-- JS will append items here --}}
                    </div>

                    <div id="emptyGuestMsg" class="text-center py-4 text-muted small d-none">
                        Chưa có khách nào. Nhấn "+ Thêm khách" để thêm.
                    </div>
                </div>
            </div>

            {{-- Submit buttons --}}
            <div class="d-flex flex-column flex-md-row gap-2 gap-md-3">
                <button type="submit" class="btn btn-primary btn-lg py-3 px-5 fw-bold rounded-3 shadow flex-fill tap d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    LƯU PHIẾU ĐĂNG KÝ
                </button>
                <a href="{{ route('visitor-tickets.index') }}" class="btn btn-outline-secondary btn-lg py-3 px-4 rounded-3 text-center">Huỷ</a>
            </div>
        </form>
    </div>
</div>

{{-- Template cho 1 khách (Card responsive) --}}
<template id="guestCardTemplate">
    <div class="guest-card-item guest-input-card p-3 border shadow-sm position-relative">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <span class="badge bg-secondary bg-opacity-10 text-dark fw-bold rounded-pill">
                Khách #<span class="guest-index">1</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-guest-btn d-flex align-items-center justify-content-center"
                    style="width:32px;height:32px;" title="Xóa khách này">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="row g-2 g-md-3">
            <div class="col-12 col-md-5">
                <label class="form-label small fw-bold text-secondary mb-1">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm guest-name-input" name="guests[INDEX][full_name]"
                       placeholder="Nhập họ và tên đầy đủ" required>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label small fw-bold text-secondary mb-1">Số CCCD / CMND</label>
                <input type="text" class="form-control form-control-sm guest-id-input" name="guests[INDEX][id_number]"
                       placeholder="Số căn cước công dân" maxlength="20">
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Số thẻ khách</label>
                <input type="text" class="form-control form-control-sm guest-card-input" name="guests[INDEX][guest_card_number]"
                       placeholder="VD: KH-001" maxlength="20">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('guestCardsContainer');
    const addBtn    = document.getElementById('addGuestBtn');
    const template  = document.getElementById('guestCardTemplate');
    const emptyMsg  = document.getElementById('emptyGuestMsg');
    const form      = document.getElementById('ticketForm');
    let rowCount    = 0;

    function updateIndices() {
        const cards = container.querySelectorAll('.guest-card-item');
        cards.forEach((card, i) => {
            card.querySelector('.guest-index').textContent = i + 1;
            card.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/guests\[\d+\]/, `guests[${i}]`);
            });
        });
        emptyMsg.classList.toggle('d-none', cards.length > 0);
    }

    function addGuest(data = null) {
        const clone = template.content.cloneNode(true);
        const card  = clone.querySelector('.guest-card-item');

        card.innerHTML = card.innerHTML.replaceAll('INDEX', rowCount);
        rowCount++;

        card.querySelector('.remove-guest-btn').addEventListener('click', function () {
            card.remove();
            updateIndices();
        });

        if (data) {
            if (data.full_name) card.querySelector('.guest-name-input').value = data.full_name;
            if (data.id_number) card.querySelector('.guest-id-input').value = data.id_number;
            if (data.guest_card_number) card.querySelector('.guest-card-input').value = data.guest_card_number;
        }

        container.appendChild(card);
        updateIndices();

        if (!data) {
            card.querySelector('.guest-name-input').focus();
        }
    }

    addBtn.addEventListener('click', () => addGuest());

    // Pre-fill old values if validation error
    @if(old('guests'))
        @foreach(old('guests', []) as $guest)
            addGuest(@json($guest));
        @endforeach
    @else
        // Mặc định thêm 1 khách đầu tiên
        addGuest();
    @endif

    // Validate before submit
    form.addEventListener('submit', function (e) {
        const cards = container.querySelectorAll('.guest-card-item');
        if (cards.length === 0) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất 1 khách!');
        }
    });
});
</script>
@endsection
