@extends('auth.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('style/auth/login.css') }}">
@endsection

@section('content')
@if (session('status'))
<div id="toast" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg">
    {{ session('status') }}
</div>

<script>
    setTimeout(function() {
        document.getElementById('toast').style.display = 'none';
    }, 2000);
</script>
@endif
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="input-login">
        @if($errors->any())
            <p class="mt-2" style="font-weight: bold;color: red;font-size: 15px;">{{ $errors->first() }}</p>
        @endif
        <span class="login-1 txt-white">Email</span>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username"
            class="block mt-1 w-full border-gray-300 rounded-2xl shadow-sm">
    </div>
    <div class="input-password relative">
        <span class="password txt-white">Mật khẩu</span>
        <div class="relative w-full">
            <input id="password" type="password" name="password" autocomplete="current-password"
                class="block mt-1 w-full border-gray-300 rounded-2xl shadow-sm pr-10 p-2">

            <!-- Icon mắt nằm trong input -->
            <span id="togglePassword" class="absolute inset-y-0 right-3 flex items-center cursor-pointer">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </span>
        </div>
    </div>
    <div class="flex-row-c">
        <a href="{{ route('password.request') }}" class="forgot-password">Quên mật khẩu ?</a>
    </div>
    <div class="p-3">
        <button class="button" type="submit"><span class="get-started">Đăng nhập</span></button>
    </div>
</form>

<span class="no-account">Bạn chưa có tài khoản? Vui lòng tạo tài khoản.</span>
<div class="p-3">
    <button class="button-4"><a href="{{ route('register') }}" class="sign-up">Đăng ký</a></button>
</div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        let passwordInput = document.getElementById('password');
        let eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2 12s2-4 10-4 10 4 10 4-2 4-10 4-10-4-10-4z"></path>
                <line x1="2" y1="2" x2="22" y2="22" stroke="currentColor" stroke-width="2"></line>
            `;
        } else {
            passwordInput.type = "password";
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    });
</script>
@endsection