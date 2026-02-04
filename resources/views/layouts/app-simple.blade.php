<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Machine App')</title>

  <!-- Bootstrap 5 & Google Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
        --primary-color: #4f46e5;
        --nav-height: 60px;
    }
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        padding-top: var(--nav-height); /* Prevent content hiding behind fixed nav */
    }
    
    /* Modern Navbar */
    .navbar-modern {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0;
        height: var(--nav-height);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .navbar-brand {
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        letter-spacing: -0.025em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .brand-icon {
        background: var(--primary-color);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nav-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-decoration: none;
        color: #475569;
    }
    .nav-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .nav-btn.active {
        color: var(--primary-color);
        background: #e0e7ff;
    }
    .nav-btn-primary {
        background: var(--primary-color);
        color: white;
    }
    .nav-btn-primary:hover {
        background: #4338ca; /* darker shade */
        color: white;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    /* Utilities */
    .tap { min-height: 48px; }
    .form-control, .form-select { min-height: 48px; font-size: 16px; } 
    
    /* Sticky Form Actions */
    .sticky-actions {
      position: sticky; bottom: 0;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(8px);
      border-top: 1px solid #e9ecef;
      padding: 12px;
      margin: 0 -12px -12px;
      z-index: 90;
    }
  </style>
</head>

<body>

<nav class="navbar navbar-modern">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    
    <!-- Brand -->
    <a class="navbar-brand" href="/dashboard">
        <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
        </div>
        MachineApp
    </a>

    <!-- Actions -->
    <!-- Language Switcher -->
    <div class="d-flex gap-2">
        <a href="{{ route('lang.switch', 'vi') }}" class="btn btn-sm {{ app()->getLocale() == 'vi' ? 'btn-primary' : 'btn-outline-secondary' }}">VN</a>
        <a href="{{ route('lang.switch', 'zh') }}" class="btn btn-sm {{ app()->getLocale() == 'zh' ? 'btn-primary' : 'btn-outline-secondary' }}">CN</a>
        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm {{ app()->getLocale() == 'en' ? 'btn-primary' : 'btn-outline-secondary' }}">EN</a>
    </div>

  </div>
</nav>

<div class="container-fluid my-4" style="max-width: {{ $maxWidth ?? '720px' }};">
  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
  @endif

  @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
