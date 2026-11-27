@extends('layouts.app-simple')
@section('title', __('messages.scan_title'))

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="mb-2 fw-bold text-primary">{{ __('messages.scan_instruction') }}</h5>
    
    <div id="webHint" class="text-muted small mb-3 {{ ($isApp ?? false) ? 'd-none' : '' }}">
      {{ __('messages.scan_hint') }}
    </div>
    <div id="appHint" class="text-muted small mb-3 {{ ($isApp ?? false) ? '' : 'd-none' }}">
      Bấm nút quét vật lý ở sườn máy PDA hoặc nhập mã thiết bị bên dưới để tra cứu.
    </div>

    <!-- Hướng dẫn cho thiết bị PDA -->
    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
      <span style="font-size: 1.2rem;">💡</span>
      <div>
        <strong>Dành cho máy PDA kiểm kho:</strong> Bấm nút quét vật lý ở 2 bên sườn máy để quét mã tức thì mà không cần camera!
      </div>
    </div>

    <!-- Khung quét Camera (Chỉ hiển thị khi dùng trên trình duyệt / Website) -->
    <div id="cameraSection" class="{{ ($isApp ?? false) ? 'd-none' : '' }}">
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
    </div>

    <!-- Nhập mã thủ công hoặc nhận từ PDA scanner -->
    <div id="inputWrapper" class="{{ ($isApp ?? false) ? 'mt-3' : 'mt-4 pt-2 border-top' }}">
      <label id="inputLabel" class="form-label fw-bold">
        {{ ($isApp ?? false) ? 'Mã thiết bị:' : __('messages.or_enter_code') }}
      </label>
      <div class="input-group input-group-lg">
        <input id="manual" class="form-control" placeholder="{{ __('messages.enter_code_placeholder') }}" autofocus autocomplete="off">
        <button class="btn btn-primary px-4 fw-bold" id="goBtn" type="button">{{ __('messages.go_btn') }}</button>
      </div>
      <div id="inputHint" class="form-text mt-2">
        {{ ($isApp ?? false) ? 'Hệ thống sẽ tự động chuyển tới trang máy ngay khi quét mã.' : __('messages.camera_blocked_hint') }}
      </div>
    </div>

    <div id="msg" class="alert alert-warning mt-3 d-none"></div>
  </div>
</div>

<!-- html5-qrcode CDN (chỉ dùng cho camera trên web) -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
  const msg = document.getElementById('msg');
  const manualInput = document.getElementById('manual');
  const camLoading = document.getElementById('camLoading');
  const camActionWrapper = document.getElementById('camActionWrapper');
  const cameraSection = document.getElementById('cameraSection');
  const inputWrapper = document.getElementById('inputWrapper');
  const inputLabel = document.getElementById('inputLabel');
  const appHint = document.getElementById('appHint');
  const webHint = document.getElementById('webHint');
  const inputHint = document.getElementById('inputHint');

  // Xác định người dùng đang dùng App (WebView / PDA) hay Website thông thường
  function checkIsApp() {
    if (@json($isApp ?? false)) return true;
    const ua = navigator.userAgent || '';
    if (/VivaApp|MachineApp|;\s*wv\)/i.test(ua)) return true;
    if (window.VivaAppInterface && typeof window.VivaAppInterface.isApp === 'function' && window.VivaAppInterface.isApp()) return true;
    const params = new URLSearchParams(window.location.search);
    if (params.get('app') === '1') return true;
    if (params.get('app') === '0' || params.get('web') === '1') return false;
    return false;
  }

  const isAppMode = checkIsApp();

  if (isAppMode) {
    if (cameraSection) cameraSection.style.display = 'none';
    if (appHint) appHint.classList.remove('d-none');
    if (webHint) webHint.classList.add('d-none');
    if (inputWrapper) {
      inputWrapper.classList.remove('border-top', 'mt-4');
      inputWrapper.classList.add('mt-3');
    }
    if (inputLabel) inputLabel.textContent = 'Mã thiết bị:';
    if (inputHint) inputHint.textContent = 'Hệ thống sẽ tự động chuyển tới trang máy ngay khi quét mã.';
  } else {
    if (cameraSection) cameraSection.style.display = 'block';
    if (appHint) appHint.classList.add('d-none');
    if (webHint) webHint.classList.remove('d-none');
  }

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

  // Tự động focus vào ô nhập mã để sẵn sàng nhận tín hiệu quét từ máy PDA
  setTimeout(() => {
    if (manualInput) manualInput.focus();
  }, 300);

  // Nhấn nút Đi
  document.getElementById('goBtn').addEventListener('click', () => {
    const code = manualInput.value.trim();
    if (!code) return showMsg('Vui lòng nhập mã thiết bị');
    goToMachine(code);
  });

  // Nhấn phím Enter trên ô nhập
  manualInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const code = manualInput.value.trim();
      if (!code) return showMsg('Vui lòng nhập mã thiết bị');
      goToMachine(code);
    }
  });

  // Lắng nghe phím quét phần cứng của máy PDA kiểm kho (nhập chuỗi ký tự nhanh và kết thúc bằng Enter)
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
      if (now - lastKeyTime > 120) {
        pdaBarcodeBuffer = '';
      }
      pdaBarcodeBuffer += e.key;
      lastKeyTime = now;
    }
  });

  // Quét QR bằng Camera (Chỉ chạy trên Website, KHÔNG chạy trên App)
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
    if (isAppMode) {
      // Đang dùng app native -> không khởi động camera
      return;
    }

    if (typeof Html5Qrcode === 'undefined') {
      if (camLoading) camLoading.innerHTML = '<span class="text-danger">Không tải được thư viện quét QR. Hãy dùng phím quét trên máy PDA hoặc nhập mã.</span>';
      if (camActionWrapper) camActionWrapper.style.display = 'block';
      return;
    }

    if (!html5QrCode) {
      html5QrCode = new Html5Qrcode("reader");
    }

    // Ưu tiên camera sau
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

  const btnRestartCam = document.getElementById('btnRestartCam');
  if (btnRestartCam) {
    btnRestartCam.addEventListener('click', () => {
      if (camLoading) {
        camLoading.style.display = 'block';
        camLoading.innerHTML = '<div class="spinner-border spinner-border-sm text-primary mb-2"></div><div>Đang thử mở lại...</div>';
      }
      initCamera();
    });
  }

  // Khởi động Camera nếu ở chế độ Website
  if (!isAppMode) {
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
      initCamera();
    } else {
      document.addEventListener('DOMContentLoaded', initCamera);
    }
  }
</script>
@endsection
