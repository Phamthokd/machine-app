<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
  @stack('modals')
  @stack('scripts')

  {{-- ===== VIVA ASSISTANT CHATBOT ===== --}}
  @if(auth()->check())
  <style>
    /* Floating button */
    #chatbot-fab {
      position: fixed;
      bottom: 24px; right: 24px;
      width: 56px; height: 56px;
      background: linear-gradient(135deg, #4f46e5, #7c3aed);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(79,70,229,.45);
      z-index: 9999;
      transition: transform .2s, box-shadow .2s;
      border: none;
    }
    #chatbot-fab:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(79,70,229,.55); }
    #chatbot-fab svg { width: 26px; height: 26px; }
    #chatbot-badge {
      position: absolute; top: -4px; right: -4px;
      background: #ef4444; color: white; font-size: 10px; font-weight: 700;
      width: 18px; height: 18px; border-radius: 50%;
      display: none; align-items: center; justify-content: center;
      border: 2px solid white;
    }

    /* Chat window */
    #chatbot-window {
      position: fixed;
      bottom: 90px; right: 24px;
      width: 380px; max-width: calc(100vw - 32px);
      height: 520px; max-height: calc(100vh - 110px);
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,.18);
      display: none; flex-direction: column;
      z-index: 9998;
      overflow: hidden;
      animation: chatSlideIn .25s ease;
    }
    @keyframes chatSlideIn {
      from { opacity: 0; transform: translateY(16px) scale(.97); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    #chatbot-window.open { display: flex; }

    /* Header */
    .chat-header {
      background: linear-gradient(135deg, #4f46e5, #7c3aed);
      padding: 14px 16px;
      display: flex; align-items: center; gap: 10px;
      flex-shrink: 0;
    }
    .chat-header-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: rgba(255,255,255,.2);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .chat-header-info { flex: 1; }
    .chat-header-name  { color: white; font-weight: 700; font-size: .9rem; line-height: 1.2; }
    .chat-header-status { color: rgba(255,255,255,.75); font-size: .72rem; display: flex; align-items: center; gap: 4px; }
    .status-dot { width: 7px; height: 7px; background: #4ade80; border-radius: 50%; animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.5} }
    .chat-close { background: none; border: none; color: rgba(255,255,255,.8); cursor: pointer; padding: 4px; border-radius: 8px; transition: background .15s; }
    .chat-close:hover { background: rgba(255,255,255,.15); color: white; }

    /* Messages */
    #chatbot-messages {
      flex: 1; overflow-y: auto; padding: 14px;
      display: flex; flex-direction: column; gap: 10px;
      scroll-behavior: smooth;
    }
    #chatbot-messages::-webkit-scrollbar { width: 4px; }
    #chatbot-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .chat-msg { display: flex; gap: 8px; max-width: 88%; }
    .chat-msg.user { align-self: flex-end; flex-direction: row-reverse; }
    .chat-msg.bot  { align-self: flex-start; }

    .chat-msg-avatar {
      width: 28px; height: 28px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; flex-shrink: 0; align-self: flex-end;
    }
    .chat-msg.bot  .chat-msg-avatar { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; }
    .chat-msg.user .chat-msg-avatar { background: #e2e8f0; }

    .chat-bubble {
      padding: 9px 13px; border-radius: 16px;
      font-size: .84rem; line-height: 1.55;
      word-break: break-word;
    }
    .chat-msg.bot  .chat-bubble { background: #f1f5f9; color: #1e293b; border-bottom-left-radius: 4px; }
    .chat-msg.user .chat-bubble { background: linear-gradient(135deg,#4f46e5,#7c3aed); color: white; border-bottom-right-radius: 4px; }

    /* Markdown in bot bubbles */
    .chat-bubble strong { font-weight: 700; }
    .chat-bubble em { font-style: italic; }
    .chat-bubble code { background: rgba(0,0,0,.07); padding: 1px 5px; border-radius: 4px; font-size: .82em; font-family: monospace; }
    .chat-bubble ul, .chat-bubble ol { padding-left: 18px; margin: 4px 0; }
    .chat-bubble li { margin: 2px 0; }
    .chat-bubble p  { margin: 4px 0; }
    .chat-bubble a  { color: #4f46e5; text-decoration: underline; }
    .chat-msg.user .chat-bubble a { color: rgba(255,255,255,.9); }

    /* Typing indicator */
    .typing-indicator { display: flex; gap: 5px; padding: 9px 13px; }
    .typing-indicator span {
      width: 7px; height: 7px; border-radius: 50%; background: #94a3b8;
      animation: bounce 1.2s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: .2s; }
    .typing-indicator span:nth-child(3) { animation-delay: .4s; }
    @keyframes bounce { 0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-6px)} }

    /* Input area */
    .chat-input-area {
      padding: 12px 14px;
      border-top: 1px solid #e2e8f0;
      display: flex; gap: 8px; flex-shrink: 0;
      background: white;
    }
    #chatbot-input {
      flex: 1; border: 1.5px solid #e2e8f0; border-radius: 12px;
      padding: 8px 12px; font-size: .85rem; outline: none;
      font-family: inherit; resize: none; height: 38px; max-height: 120px;
      overflow-y: auto; transition: border-color .2s;
      line-height: 1.4;
    }
    #chatbot-input:focus { border-color: #4f46e5; }
    #chatbot-send {
      width: 38px; height: 38px; flex-shrink: 0;
      background: linear-gradient(135deg,#4f46e5,#7c3aed);
      border: none; border-radius: 12px; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: opacity .2s;
    }
    #chatbot-send:disabled { opacity: .5; cursor: not-allowed; }

    /* Quick replies */
    .quick-replies { display: flex; gap: 6px; flex-wrap: wrap; padding: 0 14px 10px; }
    .quick-reply-btn {
      background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
      border-radius: 20px; padding: 4px 12px; font-size: .78rem; font-weight: 600;
      cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .quick-reply-btn:hover { background: #1d4ed8; color: white; border-color: #1d4ed8; }
  </style>

  <!-- Floating button -->
  <button id="chatbot-fab" onclick="toggleChat()" title="VIVA Assistant">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span id="chatbot-badge">1</span>
  </button>

  <!-- Chat window -->
  <div id="chatbot-window">
    <!-- Header -->
    <div class="chat-header">
      <div class="chat-header-avatar">🤖</div>
      <div class="chat-header-info">
        <div class="chat-header-name">VIVA Assistant</div>
        <div class="chat-header-status">
          <span class="status-dot"></span> Trực tuyến · 
          <select id="chatbot-provider-select" style="background:transparent; border:none; color:rgba(255,255,255,0.9); font-size:.72rem; font-weight:600; cursor:pointer; padding:0; outline:none;">
            <option value="openai" style="color:#333;">⚡ ChatGPT (GPT-4o)</option>
            <option value="gemini" style="color:#333;">♊ Google Gemini</option>
          </select>
        </div>
      </div>
      <button class="chat-close" onclick="toggleChat()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <!-- Messages -->
    <div id="chatbot-messages">
      <div class="chat-msg bot">
        <div class="chat-msg-avatar">🤖</div>
        <div class="chat-bubble">
          Xin chào <strong>{{ auth()->user()->name }}</strong>! 👋<br>
          Tôi là <strong>VIVA Assistant</strong> — trợ lý AI tích hợp <strong>ChatGPT & Gemini</strong>.<br><br>
          Tôi có thể giúp bạn về:
          <ul>
            <li>📋 Phiếu sửa chữa (máy, IT, công trình)</li>
            <li>📊 Kiểm tra 7S và Audit nội bộ</li>
            <li>👥 Quản lý ứng viên phỏng vấn</li>
            <li>🔧 Hướng dẫn sử dụng hệ thống</li>
          </ul>
          Bạn có thể chọn model AI ở trên góc phải thanh tiêu đề!
        </div>
      </div>
    </div>

    <!-- Quick replies -->
    <div class="quick-replies" id="quick-replies">
      <button class="quick-reply-btn" onclick="sendQuick('Hôm nay có bao nhiêu phiếu báo sửa máy, công trình, IT?')">📊 Thống kê phiếu hôm nay</button>
      <button class="quick-reply-btn" onclick="sendQuick('Hôm nay mã {{ auth()->user()->username }} đã tiếp nhận và sửa được bao nhiêu máy?')">👤 Công việc của tôi hôm nay</button>
      <button class="quick-reply-btn" onclick="sendQuick('Hướng dẫn tạo phiếu sửa máy')">📋 Tạo phiếu sửa máy</button>
      <button class="quick-reply-btn" onclick="sendQuick('Cách chuyển đơn ứng viên cho quản lý')">👥 Ứng viên phỏng vấn</button>
      <button class="quick-reply-btn" onclick="sendQuick('Kiểm tra 7S là gì?')">✅ Tiêu chuẩn 7S</button>
    </div>

    <!-- Input -->
    <div class="chat-input-area">
      <textarea id="chatbot-input" placeholder="Nhập câu hỏi..." rows="1"
        onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
      <button id="chatbot-send" onclick="sendMessage()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
          <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
      </button>
    </div>
  </div>

  <script>
  (function() {
    let isOpen    = false;
    let isLoading = false;
    let history   = []; // [{role:'user'|'model', text:'...'}]
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}";

    window.toggleChat = function() {
      isOpen = !isOpen;
      const win = document.getElementById('chatbot-window');
      if (isOpen) {
        win.classList.add('open');
        document.getElementById('chatbot-badge').style.display = 'none';
        setTimeout(() => document.getElementById('chatbot-input').focus(), 100);
      } else {
        win.classList.remove('open');
      }
    };

    window.sendQuick = function(text) {
      document.getElementById('quick-replies').style.display = 'none';
      document.getElementById('chatbot-input').value = text;
      sendMessage();
    };

    window.sendMessage = function() {
      const input = document.getElementById('chatbot-input');
      const text  = input.value.trim();
      if (!text || isLoading) return;

      const providerSelect = document.getElementById('chatbot-provider-select');
      const selectedProvider = providerSelect ? providerSelect.value : 'openai';

      input.value = '';
      autoResize(input);

      // Add user message
      appendMessage('user', text);
      history.push({ role: 'user', text });

      // Show typing
      const typingId = showTyping();
      isLoading = true;
      document.getElementById('chatbot-send').disabled = true;

      fetch('/chatbot', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify({ 
          message: text, 
          history: history.slice(-10),
          provider: selectedProvider 
        }),
      })
      .then(r => {
        if (!r.ok) {
          throw new Error('HTTP ' + r.status);
        }
        return r.json();
      })
      .then(data => {
        removeTyping(typingId);
        if (data.reply) {
          const providerTag = data.provider === 'openai' ? '⚡ ChatGPT' : (data.provider === 'gemini' ? '♊ Gemini' : '🤖 Trợ lý VIVA');
          appendMessage('bot', data.reply, providerTag);
          history.push({ role: 'model', text: data.reply });
        } else if (data.error) {
          appendMessage('bot', '⚠️ ' + data.error);
        }
      })
      .catch((err) => {
        removeTyping(typingId);
        appendMessage('bot', '⚠️ Không thể kết nối (' + (err.message || 'Lỗi mạng') + '). Vui lòng thử lại.');
      })
      .finally(() => {
        isLoading = false;
        document.getElementById('chatbot-send').disabled = false;
        document.getElementById('chatbot-input').focus();
      });
    };

    window.handleKey = function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    };

    window.autoResize = function(el) {
      el.style.height = '38px';
      el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    };

    function appendMessage(role, text, providerTag = '') {
      const msgs = document.getElementById('chatbot-messages');
      const div  = document.createElement('div');
      div.className = 'chat-msg ' + role;

      const avatar = document.createElement('div');
      avatar.className = 'chat-msg-avatar';
      avatar.textContent = role === 'bot' ? '🤖' : '{{ mb_substr(auth()->user()->name, 0, 1) }}';

      const bubble = document.createElement('div');
      bubble.className = 'chat-bubble';
      
      let html = markdownToHtml(text);
      if (role === 'bot' && providerTag) {
        html += `<div style="font-size:10px; opacity:0.65; margin-top:4px; text-align:right; font-weight:600;">${providerTag}</div>`;
      }
      bubble.innerHTML = html;

      div.appendChild(avatar);
      div.appendChild(bubble);
      msgs.appendChild(div);
      msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
      const msgs = document.getElementById('chatbot-messages');
      const id   = 'typing-' + Date.now();
      const div  = document.createElement('div');
      div.id = id; div.className = 'chat-msg bot';
      div.innerHTML = '<div class="chat-msg-avatar">🤖</div><div class="chat-bubble"><div class="typing-indicator"><span></span><span></span><span></span></div></div>';
      msgs.appendChild(div);
      msgs.scrollTop = msgs.scrollHeight;
      return id;
    }

    function removeTyping(id) {
      const el = document.getElementById(id);
      if (el) el.remove();
    }

    function markdownToHtml(text) {
      return text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/`(.+?)`/g, '<code>$1</code>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>')
        .replace(/^#{1,3}\s+(.+)$/gm, '<strong>$1</strong>')
        .replace(/^\* (.+)$/gm, '<li>$1</li>')
        .replace(/^- (.+)$/gm, '<li>$1</li>')
        .replace(/(<li>[\s\S]+?<\/li>)/g, '<ul>$1</ul>')
        .replace(/\n\n+/g, '</p><p>')
        .replace(/\n/g, '<br>')
        .replace(/^(.+)$/, '<p>$1</p>');
    }
  })();
  </script>
  @endif

</body>
</html>
