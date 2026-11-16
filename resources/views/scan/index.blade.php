@extends('layouts.app-simple')
@section('title', __('messages.scan_title'))

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="mb-2 fw-bold text-primary">{{ __('messages.scan_instruction') }}</h5>
    <div class="text-muted small mb-3">{{ __('messages.scan_hint') }}</div>

    <!-- Hướng dẫn cho thiết bị PDA -->
    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
      <span style="font-size: 1.2rem;">💡</span>
      <div>
        <strong>Dành cho máy PDA kiểm kho:</strong> Bấm nút quét vật lý ở 2 bên sườn máy để quét mã tức thì mà không cần camera!
      </div>
    </div>

    <!-- Khung quét Camera -->
    <div id="reader" style="width:100%; max-width:420px; margin:auto; min-height: 200px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; position: relative;">
      <div id="camLoading" class="text-center text-muted p-4">
        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
        <div>Đang khởi động Camera...</div>
      </div>
    </div>

    <div class="text-center my-2" id="camActionWrapper" style="display:none;">
      <button type="button" class="btn btn-outline-primary btn-sm px-3" id="btnRestartCam">
        📷 Thử lại mở Camera
      </button>
    </div>

    <!-- Nhập mã thủ công hoặc nhận từ PDA scanner -->
    <div class="mt-4 pt-2 border-top">
      <label class="form-label fw-bold">{{ __('messages.or_enter_code') }}</label>
      <div class="input-group input-group-lg">
        <input id="manual" class="form-control" placeholder="{{ __('messages.enter_code_placeholder') }}" autofocus autocomplete="off">
        <button class="btn btn-primary px-4 fw-bold" id="goBtn" type="button">{{ __('messages.go_btn') }}</button>
      </div>
      <div class="form-text mt-2">{{ __('messages.camera_blocked_hint') }}</div>
    </div>

    <div id="msg" class="alert alert-warning mt-3 d-none"></div>
  </div>
</div>

<!-- html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
  const msg = document.getElementById('msg');
  const manualInput = document.getElementById('manual');
  const camLoading = document.getElementById('camLoading');
  const camActionWrapper = document.getElementById('camActionWrapper');

  function showMsg(text) {
    if (msg) {
      msg.textContent = text;
      msg.classList.remove('d-none');
    }
  }

  function normalizeCode(text) {
    try {
      if (text.startsWith('http')) {
        const u = new URL(text);
        const parts = u.pathname.split('/').filter(Boolean);
        const last = parts[parts.length - 1];
        return last || text;
      }
    } catch (e) {}
    return text.trim();
  }

  function goToMachine(code) {
    if (!code) return;
    const ma = encodeURIComponent(normalizeCode(code));
    window.location.href = `/m/${ma}`;
  }

  // Auto focus input on load for PDA devices
  setTimeout(() => {
    if (manualInput) manualInput.focus();
  }, 400);

  // Manual / PDA Enter trigger
  document.getElementById('goBtn').addEventListener('click', () => {
    const code = manualInput.value.trim();
    if (!code) return showMsg('Vui lòng nhập mã thiết bị');
    goToMachine(code);
  });

  manualInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const code = manualInput.value.trim();
      if (!code) return showMsg('Vui lòng nhập mã thiết bị');
      goToMachine(code);
    }
  });

  // Global listener for PDA Hardware Barcode Scanner (types characters rapidly and ends with Enter)
  let pdaBarcodeBuffer = '';
  let lastKeyTime = 0;
  document.addEventListener('keydown', (e) => {
    const now = Date.now();
    if (e.key === 'Enter') {
      const scanned = manualInput.value.trim() || pdaBarcodeBuffer.trim();
      if (scanned && scanned.length >= 2) {
        e.preventDefault();
        goToMachine(scanned);
      }
      pdaBarcodeBuffer = '';
      return;
    }

    if (e.key.length === 1) {
      // If interval between keypresses > 120ms, it's normal human typing, reset buffer
      if (now - lastKeyTime > 120) {
        pdaBarcodeBuffer = '';
      }
      pdaBarcodeBuffer += e.key;
      lastKeyTime = now;
    }
  });

  // Camera QR Scanner
  let html5QrCode = null;
  const qrConfig = {
    fps: 10,
    qrbox: { width: 240, height: 240 },
    aspectRatio: 1.0
  };

  function onScanSuccess(decodedText) {
    const code = normalizeCode(decodedText);
    if (html5QrCode) {
      html5QrCode.stop().then(() => goToMachine(code)).catch(() => goToMachine(code));
    } else {
      goToMachine(code);
    }
  }

  function initCamera() {
    if (typeof Html5Qrcode === 'undefined') {
      if (camLoading) camLoading.innerHTML = '<span class="text-danger">Không tải được thư viện quét QR. Hãy dùng phím quét trên máy PDA hoặc nhập mã.</span>';
      if (camActionWrapper) camActionWrapper.style.display = 'block';
      return;
    }

    if (!html5QrCode) {
      html5QrCode = new Html5Qrcode("reader");
    }

    // Try back camera directly
    html5QrCode.start(
      { facingMode: "environment" },
      qrConfig,
      onScanSuccess,
      () => {}
    ).then(() => {
      if (camLoading) camLoading.style.display = 'none';
      if (camActionWrapper) camActionWrapper.style.display = 'none';
    }).catch(err => {
      console.warn("facingMode failed, trying getCameras fallback...", err);
      Html5Qrcode.getCameras().then(cameras => {
        if (!cameras || cameras.length === 0) {
          if (camLoading) camLoading.innerHTML = '<div class="text-warning">Không tìm thấy Camera. Bạn có thể dùng nút quét sườn máy PDA.</div>';
          if (camActionWrapper) camActionWrapper.style.display = 'block';
          return;
        }
        const backCam = cameras.find(c => (c.label || '').toLowerCase().includes('back')) || cameras[cameras.length - 1];
        html5QrCode.start(
          backCam.id,
          qrConfig,
          onScanSuccess,
          () => {}
        ).then(() => {
          if (camLoading) camLoading.style.display = 'none';
          if (camActionWrapper) camActionWrapper.style.display = 'none';
        }).catch(err2 => {
          if (camLoading) camLoading.innerHTML = '<div class="text-danger small">Không thể kích hoạt Camera: ' + err2 + '<br>Hãy dùng nút quét sườn máy PDA.</div>';
          if (camActionWrapper) camActionWrapper.style.display = 'block';
        });
      }).catch(err3 => {
        if (camLoading) camLoading.innerHTML = '<div class="text-danger small">Lỗi truy cập Camera: ' + err3 + '<br>Hãy dùng nút quét sườn máy PDA.</div>';
        if (camActionWrapper) camActionWrapper.style.display = 'block';
      });
    });
  }

  document.getElementById('btnRestartCam').addEventListener('click', () => {
    if (camLoading) {
      camLoading.style.display = 'block';
      camLoading.innerHTML = '<div class="spinner-border spinner-border-sm text-primary mb-2"></div><div>Đang thử mở lại...</div>';
    }
    initCamera();
  });

  // Start on load
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    initCamera();
  } else {
    document.addEventListener('DOMContentLoaded', initCamera);
  }
</script>
@endsection
