@extends('layouts.app')

@section('title', 'Вход в систему — Aether Airways')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/auth/authorization.css') }}?v=1">
@endsection

@section('content')
<main class="auth-page-wrapper">
    <div class="auth-split-card">

        <div class="auth-visual-col">
            <img src="{{ asset('image/mascot_offer_sochi.jpg') }}" alt="Aether Airways Supersonic Liner" onerror="this.src='{{ asset('image/air.jpg') }}'">
        </div>

        <div class="auth-form-col">
            <a href="{{ route('home') }}" class="auth-brand-badge" title="На главную">
                <span class="brand-text-logo">AETHER AIRWAYS</span>
            </a>

            <h1 class="auth-title">С возвращением</h1>
            <p class="auth-subtitle">
                Введите ваши учетные данные или <a href="{{ route('register') }}">создайте аккаунт</a>
            </p>

            @if($errors->has('email') && !old('email'))
                <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #b91c1c; font-size: 14px;">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form class="auth-form" action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
                @csrf
                <div class="auth-input-group">
                    <label class="auth-input-label" for="authEmail">Электронная почта (логин)</label>
                    <input type="email"
                           id="authEmail"
                           class="auth-input-clean @error('email') input-error @enderror"
                           name="email"
                           placeholder="alex@gmail.com"
                           value="{{ old('email', 'hello.alex@gmail.com') }}"
                           required
                           autofocus>
                    @error('email')
                        <span class="field-validation-error">{{ $message }}</span>
                    @enderror
                    <span class="field-validation-error client-err" id="clientErrLoginEmail" style="display:none;"></span>
                </div>

                <div class="auth-input-group">
                    <label class="auth-input-label" for="authPassInput">Пароль</label>
                    <div class="auth-password-wrap">
                        <input type="password"
                               id="authPassInput"
                               class="auth-input-clean @error('password') input-error @enderror"
                               name="password"
                               placeholder="••••••••"
                               value="password123"
                               required>
                        <button type="button" class="auth-eye-btn" onclick="const p = document.getElementById('authPassInput'); p.type = p.type === 'password' ? 'text' : 'password';" aria-label="Показать пароль">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="field-validation-error">{{ $message }}</span>
                    @enderror
                    <span class="field-validation-error client-err" id="clientErrLoginPass" style="display:none;"></span>
                </div>

                <div class="auth-row-options">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" name="remember" value="1" checked>
                        <span>Запомнить меня</span>
                    </label>
                    <span class="auth-forgot-link" style="color: var(--text-secondary); cursor: pointer;" onclick="alert('Демо-пользователь: hello.alex@gmail.com / пароль: password123\nАдминистратор: admin@aether.local / пароль: admin123')">
                        Демо-доступы
                    </span>
                </div>

                <button type="submit" class="btn btn-auth-submit">
                    Войти в аккаунт
                </button>
            </form>

            <div class="auth-bottom-switch">
                Впервые у нас? <a href="{{ route('register') }}">Создать аккаунт (+1 000 миль)</a>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('authEmail');
        const passInput = document.getElementById('authPassInput');

        form.addEventListener('submit', function (e) {
            let hasError = false;
            document.querySelectorAll('.client-err').forEach(el => el.style.display = 'none');
            emailInput.classList.remove('input-error');
            passInput.classList.remove('input-error');

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                document.getElementById('clientErrLoginEmail').textContent = 'Пожалуйста, введите корректный адрес электронной почты.';
                document.getElementById('clientErrLoginEmail').style.display = 'block';
                emailInput.classList.add('input-error');
                hasError = true;
            }

            if (!passInput.value || passInput.value.length < 6) {
                document.getElementById('clientErrLoginPass').textContent = 'Пожалуйста, введите пароль (не менее 6 символов).';
                document.getElementById('clientErrLoginPass').style.display = 'block';
                passInput.classList.add('input-error');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
