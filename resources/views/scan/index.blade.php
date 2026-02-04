@extends('layouts.app-simple')
@section('title', __('messages.scan_title'))

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="mb-2">{{ __('messages.scan_instruction') }}</h5>
    <div class="text-muted small mb-3">{{ __('messages.scan_hint') }}</div>

    <div id="reader" style="width:100%; max-width:420px; margin:auto;"></div>

    <div class="mt-3">
      <label class="form-label">{{ __('messages.or_enter_code') }}</label>
      <div class="input-group">
        <input id="manual" class="form-control" placeholder="{{ __('messages.enter_code_placeholder') }}">
        <button class="btn btn-primary" id="goBtn">{{ __('messages.go_btn') }}</button>
      </div>
      <div class="form-text">{{ __('messages.camera_blocked_hint') }}</div>
    </div>

    <div id="msg" class="alert alert-warning mt-3 d-none"></div>
  </div>
</div>

<!-- html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
  const msg = document.getElementById('msg');

  function showMsg(text) {
    msg.textContent = text;
    msg.classList.remove('d-none');
  }

  function normalizeCode(text) {
    // Nếu QR chứa URL dạng .../m/MAY-001 thì lấy phần cuối
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
    const ma = encodeURIComponent(code);
    window.location.href = `/m/${ma}`;
  }

  // Manual
  document.getElementById('goBtn').addEventListener('click', () => {
    const code = document.getElementById('manual').value.trim();
    if (!code) return showMsg('Vui lòng nhập mã thiết bị');
    goToMachine(code);
  });

  // QR scan
  const html5QrCode = new Html5Qrcode("reader");
  const config = {
    fps: 10,
    qrbox: { width: 260, height: 260 },
    aspectRatio: 1.0
  };

  Html5Qrcode.getCameras().then(cameras => {
    if (!cameras || cameras.length === 0) {
      showMsg('{{ __('messages.camera_not_found') }}');
      return;
    }

    // Ưu tiên camera sau (back camera) nếu có
    const backCam = cameras.find(c => (c.label || '').toLowerCase().includes('back')) || cameras[cameras.length - 1];

    html5QrCode.start(
      { deviceId: { exact: backCam.id } },
      config,
      (decodedText) => {
        const code = normalizeCode(decodedText);
        // Dừng scan để không quét lại liên tục
        html5QrCode.stop().then(() => goToMachine(code));
      },
      (errorMessage) => { /* ignore */ }
    ).catch(err => {
      showMsg('{{ __('messages.camera_error') }}: ' + err);
    });

  }).catch(err => {
    showMsg('{{ __('messages.camera_error') }}: ' + err);
  });
</script>
@endsection
