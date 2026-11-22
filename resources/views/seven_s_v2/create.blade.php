@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', 'Tạo đợt đánh giá 7S 2.0')

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">

  {{-- Back & Title --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('seven-s-v2.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
      </a>
      <div>
        <h4 class="fw-bold mb-0 text-dark">Đánh giá 7S 2.0</h4>
        <div class="text-muted small">Người đánh giá: <strong>{{ auth()->user()->name }}</strong> &bull; Ngày: {{ now()->format('d/m/Y') }}</div>
      </div>
    </div>
  </div>

  @if(isset($errors) && $errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm mb-4">
      <div class="fw-bold mb-1">Vui lòng kiểm tra lại thông tin:</div>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="sevenSForm" action="{{ route('seven-s-v2.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Bộ phận & Thông tin cơ bản --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4">
        <label class="form-label fw-bold text-dark fs-6 mb-2">
          1. Lựa chọn bộ phận kiểm tra <span class="text-danger">*</span>
        </label>
        <select name="department" id="departmentSelect" class="form-select form-select-lg rounded-3 fw-semibold" required>
          <option value="" disabled selected>-- Chọn bộ phận cần đánh giá 7S --</option>
          @foreach($departments as $dept)
            <option value="{{ $dept }}" {{ (old('department', $selectedDept) == $dept) ? 'selected' : '' }}>
              {{ $dept }}
            </option>
          @endforeach
        </select>
        <div class="form-text mt-1 text-muted">
          Người kiểm tra tiến hành quan sát thực tế tại bộ phận này và ghi nhận các lỗi vi phạm 7S (nếu có).
        </div>
      </div>
    </div>

    {{-- Sticky Live Penalty Score Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top bg-white border-top border-4 border-primary" style="top: 70px; z-index: 100;">
      <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="fs-5">📊</span>
          <div>
            <div class="small text-secondary fw-semibold">Tổng điểm phạt:</div>
            <div id="displayTotalPenalty" class="fs-4 fw-bold text-success">0 điểm</div>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-warning bg-opacity-20 text-dark border border-warning px-3 py-2 rounded-pill">
            Lỗi nhẹ: <span id="displayLightCount" class="fw-bold">0</span> (-1đ)
          </span>
          <span class="badge bg-danger bg-opacity-20 text-danger border border-danger px-3 py-2 rounded-pill">
            Lỗi nặng: <span id="displayHeavyCount" class="fw-bold">0</span> (-2đ)
          </span>
        </div>
      </div>
    </div>

    {{-- Danh sách lỗi vi phạm (Động) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h5 class="fw-bold mb-1 text-dark">2. Ghi nhận lỗi vi phạm 7S</h5>
          <div class="text-muted small">Nếu bộ phận có lỗi, hãy thêm nội dung lỗi và chọn mức phạt tương ứng.</div>
        </div>
        <button type="button" id="btnAddIssue" class="btn btn-primary d-flex align-items-center gap-1.5 shadow-sm rounded-3 fw-semibold px-3 py-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
          <span>Thêm lỗi vi phạm</span>
        </button>
      </div>

      <div class="card-body p-4">
        {{-- Empty State when no issues --}}
        <div id="emptyIssueState" class="text-center py-5 border border-dashed rounded-4 bg-light">
          <div class="mb-2 fs-1">✨</div>
          <div class="fw-bold text-dark fs-6">Hiện tại chưa ghi nhận lỗi nào</div>
          <div class="text-muted small max-w-sm mx-auto mt-1 mb-3">
            Nếu bộ phận được kiểm tra hoàn toàn sạch sẽ, ngăn nắp và đạt chuẩn 7S, bạn có thể lưu phiếu ngay với kết quả <strong>0 điểm trừ</strong>.
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-medium" onclick="document.getElementById('btnAddIssue').click()">
            + Phát hiện lỗi vi phạm? Bấm vào đây
          </button>
        </div>

        {{-- Dynamic Container for Issue Cards --}}
        <div id="issuesContainer" class="d-flex flex-column gap-3"></div>
      </div>
    </div>

    {{-- Ghi chú chung --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4">
        <label class="form-label fw-bold text-dark mb-1">3. Ghi chú chung đợt kiểm tra (Tùy chọn)</label>
        <textarea name="general_note" class="form-control rounded-3" rows="2" placeholder="Nhập nhận xét tổng quan về tinh thần 7S của bộ phận...">{{ old('general_note') }}</textarea>
      </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
      <a href="{{ route('seven-s-v2.index') }}" class="btn btn-light border px-4 py-2.5 rounded-3 fw-medium">Hủy bỏ</a>
      <button type="submit" class="btn btn-primary btn-lg px-5 py-2.5 rounded-3 fw-bold shadow">
        Lưu & Hoàn thành đánh giá
      </button>
    </div>

  </form>
</div>

{{-- Template cho từng thẻ lỗi --}}
<template id="issueTemplate">
  <div class="card border border-2 issue-card rounded-4 shadow-sm p-3 position-relative" data-index="__INDEX__">
    <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-dark rounded-pill px-2.5 py-1">Lỗi #<span class="issue-number">1</span></span>
        <span class="text-muted small">Chọn mức phạt bên dưới</span>
      </div>
      <button type="button" class="btn btn-link text-danger p-0 text-decoration-none btn-remove-issue" title="Xóa lỗi này">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        <span class="small fw-semibold ms-1">Xóa</span>
      </button>
    </div>

    {{-- Severity Selection --}}
    <div class="mb-3">
      <label class="form-label small fw-bold text-dark mb-1.5">Mức độ lỗi & Điểm trừ <span class="text-danger">*</span></label>
      <div class="d-flex gap-3">
        <label class="severity-option flex-fill border rounded-3 p-2.5 d-flex align-items-center gap-2 cursor-pointer bg-light">
          <input type="radio" name="issues[__INDEX__][severity]" value="light" class="form-check-input severity-radio" checked>
          <div>
            <div class="fw-bold text-warning-emphasis small">Lỗi nhẹ (-1 điểm)</div>
            <div class="text-muted" style="font-size:0.75rem;">Vi phạm mức độ nhỏ, dễ khắc phục ngay</div>
          </div>
        </label>
        <label class="severity-option flex-fill border rounded-3 p-2.5 d-flex align-items-center gap-2 cursor-pointer bg-light">
          <input type="radio" name="issues[__INDEX__][severity]" value="heavy" class="form-check-input severity-radio">
          <div>
            <div class="fw-bold text-danger small">Lỗi nặng (-2 điểm)</div>
            <div class="text-muted" style="font-size:0.75rem;">Vi phạm nghiêm trọng, mất an toàn, tái phạm</div>
          </div>
        </label>
      </div>
    </div>

    {{-- Issue Content --}}
    <div class="mb-3">
      <label class="form-label small fw-bold text-dark mb-1">Nội dung lỗi vi phạm <span class="text-danger">*</span></label>
      <textarea name="issues[__INDEX__][content]" class="form-control rounded-3" rows="2" placeholder="Mô tả cụ thể lỗi phát hiện được (VD: Để dầu máy chảy ra sàn chuyền 3, rác thải chưa gom...)" required></textarea>
    </div>

    {{-- Location & Photos --}}
    <div class="row g-2 mb-1">
      <div class="col-12 col-md-6">
        <label class="form-label small fw-semibold text-secondary mb-1">Vị trí cụ thể (Tùy chọn)</label>
        <input type="text" name="issues[__INDEX__][location_detail]" class="form-control form-control-sm rounded-3" placeholder="VD: Bàn kiểm hàng số 2, Cửa kho A...">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label small fw-semibold text-secondary mb-1">Ảnh chụp hiện trường vi phạm</label>
        <input type="file" name="issues[__INDEX__][images][]" multiple accept="image/*" class="form-control form-control-sm rounded-3 input-issue-images">
      </div>
    </div>
  </div>
</template>

<style>
  .cursor-pointer { cursor: pointer; }
  .severity-option {
    transition: all 0.2s ease;
  }
  .severity-option:has(input:checked) {
    border-color: var(--bs-primary) !important;
    background-color: rgba(79, 70, 229, 0.06) !important;
  }
</style>

<script>
  let issueCounter = 0;
  const issuesContainer = document.getElementById('issuesContainer');
  const emptyIssueState = document.getElementById('emptyIssueState');
  const btnAddIssue = document.getElementById('btnAddIssue');
  const templateHtml = document.getElementById('issueTemplate').innerHTML;

  function updateSummary() {
    const cards = document.querySelectorAll('.issue-card');
    let light = 0;
    let heavy = 0;

    cards.forEach((card, idx) => {
      // Update ordinal number
      const numSpan = card.querySelector('.issue-number');
      if (numSpan) numSpan.textContent = idx + 1;

      // Count severity
      const selected = card.querySelector('.severity-radio:checked');
      if (selected && selected.value === 'heavy') {
        heavy++;
        card.classList.remove('border-warning');
        card.classList.add('border-danger');
      } else {
        light++;
        card.classList.remove('border-danger');
        card.classList.add('border-warning');
      }
    });

    const totalPenalty = (light * -1) + (heavy * -2);

    document.getElementById('displayLightCount').textContent = light;
    document.getElementById('displayHeavyCount').textContent = heavy;
    const penaltyEl = document.getElementById('displayTotalPenalty');

    if (totalPenalty < 0) {
      penaltyEl.textContent = `${totalPenalty} điểm`;
      penaltyEl.className = 'fs-4 fw-bold text-danger';
    } else {
      penaltyEl.textContent = '0 điểm';
      penaltyEl.className = 'fs-4 fw-bold text-success';
    }

    if (cards.length === 0) {
      emptyIssueState.classList.remove('d-none');
    } else {
      emptyIssueState.classList.add('d-none');
    }
  }

  function addIssueCard() {
    const index = issueCounter++;
    const cardHtml = templateHtml.replaceAll('__INDEX__', index);
    const div = document.createElement('div');
    div.innerHTML = cardHtml.trim();
    const cardElement = div.firstChild;

    // Remove listener
    cardElement.querySelector('.btn-remove-issue').addEventListener('click', () => {
      cardElement.remove();
      updateSummary();
    });

    // Radio change listener
    cardElement.querySelectorAll('.severity-radio').forEach(r => {
      r.addEventListener('change', updateSummary);
    });

    issuesContainer.appendChild(cardElement);
    updateSummary();

    // Scroll smoothly to newly added card
    cardElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  btnAddIssue.addEventListener('click', addIssueCard);

  // Initialize summary on load
  updateSummary();
</script>
@endsection
