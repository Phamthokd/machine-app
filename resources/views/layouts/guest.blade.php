<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Machine App') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap 5 & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            margin: 20px;
        }

        .login-header {
            text-align: center;
            padding: 40px 30px 20px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            background: #4f46e5;
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }

        .btn-primary {
            min-height: 48px;
            border-radius: 12px;
            font-weight: 600;
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .form-control {
            min-height: 48px;
            border-radius: 12px;
            padding-left: 16px;
            border-color: #e2e8f0;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="login-header">
            <div class="d-flex justify-content-center mb-3">
                <img src="{{ asset('images/viva-logo.png') }}" alt="Logo" class="rounded-4 shadow" style="width: 72px; height: 72px; object-fit: cover;">
            </div>
            <h4 class="fw-bold mb-1 text-dark">Welcome Back!</h4>
            <div class="text-secondary small">Vui lòng đăng nhập để tiếp tục</div>
        </div>

        <div class="p-4 pt-0">
            {{ $slot }}
        </div>

        <div class="bg-light p-3 text-center small text-secondary">
            &copy; {{ date('Y') }} Machine App System
        </div>
    </div>

</body>

</html>