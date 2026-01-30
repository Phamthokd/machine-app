<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Machine App')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Tối ưu mobile */
    .tap { min-height: 48px; }                 /* vùng bấm to */
    .form-control, .form-select { min-height: 48px; font-size: 16px; } /* iOS tránh zoom */
    .sticky-actions {
      position: sticky; bottom: 0;
      background: rgba(248,249,250,.95);
      backdrop-filter: blur(8px);
      border-top: 1px solid #e9ecef;
      padding: 12px;
      margin: 0 -12px -12px; /* khớp padding card */
    }
  </style>
</head>

<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/m/MAY-001">Machine App</a>
    <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-light tap" href="/repairs">Phiếu sửa</a>
    </div>
  </div>
</nav>
<a class="btn btn-sm btn-outline-light tap" href="/scan">Quét QR</a>

<div class="container-fluid my-3" style="max-width: 720px;">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
