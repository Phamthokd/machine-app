<x-guest-layout>
    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success mb-4 rounded-3 text-small">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input id="username" class="form-control" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Nhập username...">
            @if($errors->has('username'))
                <div class="text-danger small mt-1">
                    {{ $errors->first('username') }}
                </div>
            @endif
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @if($errors->has('password'))
                <div class="text-danger small mt-1">
                    {{ $errors->first('password') }}
                </div>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label text-secondary small">
                Ghi nhớ đăng nhập
            </label>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary shadow-sm tap">
                ĐĂNG NHẬP
            </button>
        </div>
        
        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-decoration-none small text-secondary" href="{{ route('password.request') }}">
                    Quên mật khẩu?
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
