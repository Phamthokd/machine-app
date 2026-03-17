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
      padding-top: var(--nav-height);
      /* Prevent content hiding behind fixed nav */
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
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
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
      background: #4338ca;
      /* darker shade */
      color: white;
      box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    /* Utilities */
    .tap {
      min-height: 48px;
    }

    .form-control,
    .form-select {
      min-height: 48px;
      font-size: 16px;
    }

    /* Sticky Form Actions */
    .sticky-actions {
      position: sticky;
      bottom: 0;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border-top: 1px solid #e9ecef;
      padding: 12px;
      margin: 0 -12px -12px;
      z-index: 90;
    }

    .notification-read {
      opacity: 0.5;
    }

    .notification-dropdown {
      width: 320px;
    }

    @media (max-width: 576px) {
      .notification-dropdown {
        width: calc(100vw - 20px);
        position: fixed !important;
        left: 10px !important;
        right: 10px !important;
        top: 64px !important;
        transform: none !important;
      }

      .navbar-modern .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
      }

      .navbar-brand {
        font-size: 1.1rem;
        gap: 4px;
      }

      .nav-btn-lang {
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
      }
      
      .d-flex.gap-2.align-items-center {
        gap: 0.35rem !important;
      }
    }
  </style>
</head>

<body>

  @php
    $recentNotifications = collect();
    $unreadCount = 0;
    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
      $recentNotifications = auth()->user()->notifications()->latest()->limit(10)->get();
      $unreadCount = auth()->user()->unreadNotifications()->count();
    }
  @endphp

  <nav class="navbar navbar-modern">
    <div class="container-fluid d-flex justify-content-between align-items-center">

      <!-- Brand -->
      <a class="navbar-brand" href="/dashboard">
        <div class="brand-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
          </svg>
        </div>
        MachineApp
      </a>

      <!-- Actions -->
      <div class="d-flex gap-2 align-items-center">
        @auth
        <div class="dropdown">
          <button
            class="btn btn-sm btn-outline-secondary position-relative"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="Notifications">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 16a2 2 0 0 0 1.985-1.75h-3.97A2 2 0 0 0 8 16Zm.104-14.995a1 1 0 0 0-.208 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7a5.002 5.002 0 0 0-4.896-4.995Z" />
            </svg>
            @if($unreadCount > 0)
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-notification bg-danger">{{ $unreadCount }}</span>
            @endif
          </button>

          <div class="dropdown-menu dropdown-menu-end p-0 overflow-hidden notification-dropdown">
            <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
              <strong class="small">{{ __('messages.notifications') }}</strong>
              @if($unreadCount > 0)
                <a href="{{ route('notifications.read_all') }}" class="text-primary x-small fw-bold text-decoration-none" style="font-size: 10px;">{{ __('messages.mark_all_as_read') }}</a>
              @endif
            </div>

            <div style="max-height: 280px; overflow-y: auto;">
              @forelse($recentNotifications as $notification)
                <a class="dropdown-item py-2 border-bottom js-notification-link {{ $notification->read_at ? 'notification-read' : '' }}" 
                   href="{{ route('notifications.open', $notification->id) }}" 
                   id="notification-{{ $notification->id }}">
                  <div class="fw-semibold small">{{ __($notification->data['title'] ?? 'Thong bao', $notification->data['params'] ?? []) }}</div>
                  <div class="small text-muted">{{ __($notification->data['message'] ?? '', $notification->data['params'] ?? []) }}</div>
                  <div class="small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
              @empty
                <div class="px-3 py-3 small text-muted">{{ __('messages.no_notifications') }}</div>
              @endforelse
            </div>
          </div>
        </div>
        @endauth

        <a href="{{ route('lang.switch', 'vi') }}" class="btn btn-sm nav-btn-lang {{ app()->getLocale() == 'vi' ? 'btn-primary' : 'btn-outline-secondary' }}">VN</a>
        <a href="{{ route('lang.switch', 'zh') }}" class="btn btn-sm nav-btn-lang {{ app()->getLocale() == 'zh' ? 'btn-primary' : 'btn-outline-secondary' }}">CN</a>
        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm nav-btn-lang {{ app()->getLocale() == 'en' ? 'btn-primary' : 'btn-outline-secondary' }}">EN</a>
      </div>

    </div>
  </nav>

  <div class="container-fluid my-4" style="max-width: <?php echo e($maxWidth ?? '1100px'); ?>;">

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
      {{ session('success') }}
    </div>
    @endif

    @yield('content')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const notificationDropdown = document.querySelector(".dropdown-menu");
      if (notificationDropdown) {
        notificationDropdown.addEventListener("click", function(e) {
          e.stopPropagation();
        });
      }

      const notifLinks = document.querySelectorAll(".js-notification-link");
      const badge = document.querySelector(".badge-notification");

      notifLinks.forEach(link => {
        link.addEventListener("click", function(e) {
          // If already marked as read visually, let the normal navigation happen
          if (this.classList.contains("notification-read")) {
            return; 
          }

          e.preventDefault();
          e.stopPropagation(); // Keep dropdown open

          const url = this.getAttribute("href");
          
          fetch(url, {
            method: "GET",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "Accept": "application/json"
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === "success") {
              // Mark as read visually
              this.classList.add("notification-read");
              
              // Update badge count
              if (badge) {
                let currentCount = parseInt(badge.textContent);
                if (!isNaN(currentCount) && currentCount > 0) {
                  currentCount--;
                  badge.textContent = currentCount;
                  if (currentCount === 0) {
                    badge.remove();
                  }
                }
              }
            }
          })
          .catch(error => console.error("Error marking notification as read:", error));
        });
      });
    });
  </script>
</body>

</html>
