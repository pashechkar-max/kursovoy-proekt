@extends('layouts.app')

@section('title', 'Регистрация пассажира — Aether Airways')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/auth/registration.css') }}?v=1">
@endsection

@section('content')
<main class="register-page-wrapper">
    <div class="register-split-card">

        <div class="register-visual-col">
            <img src="{{ asset('image/mascot_miles.jpg') }}" alt="Aether Airways Cabin Interior" onerror="this.src='{{ asset('image/air.jpg') }}'">
        </div>

        <div class="register-form-col">
            <a href="{{ route('home') }}" class="register-brand-badge" title="На главную">
                <span class="brand-text-logo">AETHER AIRWAYS</span>
            </a>

            <h1 class="register-title">Создание аккаунта</h1>
            <p class="register-subtitle">
                Заполните данные для доступа к личному кабинету и бронированиям
            </p>

            <div class="welcome-miles-capsule">
                <span>⭐ +1 000 приветственных миль в подарок при регистрации</span>
            </div>

            <!-- Общий блок ошибок сервера -->
            @if($errors->any())
                <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #b91c1c; font-size: 13px;">
                    <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                    <ul style="margin: 6px 0 0 0; padding-left: 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="register-form" action="{{ route('register') }}" method="POST" id="registrationForm" novalidate>
                @csrf

                <div class="register-grid-two">
                    <div class="register-input-group">
                        <label class="register-input-label" for="regFirstName">Имя</label>
                        <input type="text" 
                               id="regFirstName" 
                               class="register-input-clean @error('first_name') input-error @enderror" 
                               name="first_name" 
                               placeholder="Александра" 
                               value="{{ old('first_name') }}" 
                               required 
                               minlength="2"
                               pattern="^[a-zA-Zа-яА-ЯёЁ\s\-]+$"
                               autofocus>
                        @error('first_name')
                            <span class="field-validation-error">{{ $message }}</span>
                        @enderror
                        <span class="field-validation-error client-err" id="clientErrFirstName" style="display:none;"></span>
                    </div>

                    <div class="register-input-group">
                        <label class="register-input-label" for="regLastName">Фамилия</label>
                        <input type="text" 
                               id="regLastName" 
                               class="register-input-clean @error('last_name') input-error @enderror" 
                               name="last_name" 
                               placeholder="Александрова" 
                               value="{{ old('last_name') }}" 
                               required 
                               minlength="2"
                               pattern="^[a-zA-Zа-яА-ЯёЁ\s\-]+$">
                        @error('last_name')
                            <span class="field-validation-error">{{ $message }}</span>
                        @enderror
                        <span class="field-validation-error client-err" id="clientErrLastName" style="display:none;"></span>
                    </div>
                </div>

                <div class="register-input-group">
                    <label class="register-input-label" for="regEmail">Электронная почта (логин)</label>
                    <input type="email" 
                           id="regEmail" 
                           class="register-input-clean @error('email') input-error @enderror" 
                           name="email" 
                           placeholder="alex@gmail.com" 
                           value="{{ old('email') }}" 
                           required>
                    @error('email')
                        <span class="field-validation-error">{{ $message }}</span>
                    @enderror
                    <span class="field-validation-error client-err" id="clientErrEmail" style="display:none;"></span>
                </div>

                <div class="register-grid-two">
                    <div class="register-input-group">
                        <label class="register-input-label" for="regPass">Пароль</label>
                        <input type="password" 
                               id="regPass" 
                               class="register-input-clean @error('password') input-error @enderror" 
                               name="password" 
                               placeholder="••••••••" 
                               required 
                               minlength="8">
                        @error('password')
                            <span class="field-validation-error">{{ $message }}</span>
                        @enderror
                        <span class="field-validation-error client-err" id="clientErrPass" style="display:none;"></span>
                    </div>

                    <div class="register-input-group">
                        <label class="register-input-label" for="regPassConfirm">Повторите пароль</label>
                        <input type="password" 
                               id="regPassConfirm" 
                               class="register-input-clean @error('password_confirmation') input-error @enderror" 
                               name="password_confirmation" 
                               placeholder="••••••••" 
                               required 
                               minlength="8">
                        @error('password_confirmation')
                            <span class="field-validation-error">{{ $message }}</span>
                        @enderror
                        <span class="field-validation-error client-err" id="clientErrPassConfirm" style="display:none;"></span>
                    </div>
                </div>

                <!-- Индикатор парольной политики (Требование 2) -->
                <div class="password-policy-box">
                    <div style="font-weight: 600; color: #334155; margin-bottom: 6px;">Требования к безопасности пароля:</div>
                    <div class="policy-item" id="policyLength"><span class="policy-dot">○</span> Не менее 8 символов</div>
                    <div class="policy-item" id="policyLetter"><span class="policy-dot">○</span> Содержит буквы (A-Z, а-я)</div>
                    <div class="policy-item" id="policyNumber"><span class="policy-dot">○</span> Содержит хотя бы одну цифру (0-9)</div>
                    <div class="policy-item" id="policyMatch"><span class="policy-dot">○</span> Пароли совпадают</div>
                </div>

                <button type="submit" class="btn btn-register-submit" style="margin-top: 18px;">
                    Зарегистрироваться (+1 000 миль)
                </button>
            </form>

            <div class="register-bottom-switch">
                Уже есть личный кабинет? <a href="{{ route('login') }}">Войти</a>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passInput = document.getElementById('regPass');
        const passConfirm = document.getElementById('regPassConfirm');
        const emailInput = document.getElementById('regEmail');
        const form = document.getElementById('registrationForm');

        const policyLength = document.getElementById('policyLength');
        const policyLetter = document.getElementById('policyLetter');
        const policyNumber = document.getElementById('policyNumber');
        const policyMatch = document.getElementById('policyMatch');

        function updatePolicyState(elem, isValid) {
            const dot = elem.querySelector('.policy-dot');
            if (isValid) {
                elem.classList.add('valid');
                dot.textContent = '✓';
            } else {
                elem.classList.remove('valid');
                dot.textContent = '○';
            }
        }

        function validatePasswordLive() {
            const val = passInput.value;
            const confirmVal = passConfirm.value;

            // 1. Длина минимум 8
            const hasLength = val.length >= 8;
            updatePolicyState(policyLength, hasLength);

            // 2. Буквы
            const hasLetter = /[a-zA-Zа-яА-ЯёЁ]/.test(val);
            updatePolicyState(policyLetter, hasLetter);

            // 3. Цифры
            const hasNumber = /\d/.test(val);
            updatePolicyState(policyNumber, hasNumber);

            // 4. Совпадение
            const isMatch = val.length > 0 && val === confirmVal;
            updatePolicyState(policyMatch, isMatch);

            return hasLength && hasLetter && hasNumber && isMatch;
        }

        passInput.addEventListener('input', validatePasswordLive);
        passConfirm.addEventListener('input', validatePasswordLive);

        // Клиентская валидация перед отправкой (Требование 4)
        form.addEventListener('submit', function (e) {
            let hasError = false;

            // Скрываем старые ошибки
            document.querySelectorAll('.client-err').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.register-input-clean').forEach(el => el.classList.remove('input-error'));

            // Проверка имени
            const firstName = document.getElementById('regFirstName');
            if (!firstName.value.trim() || firstName.value.trim().length < 2) {
                document.getElementById('clientErrFirstName').textContent = 'Имя должно содержать минимум 2 буквы.';
                document.getElementById('clientErrFirstName').style.display = 'block';
                firstName.classList.add('input-error');
                hasError = true;
            }

            // Проверка фамилии
            const lastName = document.getElementById('regLastName');
            if (!lastName.value.trim() || lastName.value.trim().length < 2) {
                document.getElementById('clientErrLastName').textContent = 'Фамилия должна содержать минимум 2 буквы.';
                document.getElementById('clientErrLastName').style.display = 'block';
                lastName.classList.add('input-error');
                hasError = true;
            }

            // Проверка email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                document.getElementById('clientErrEmail').textContent = 'Пожалуйста, введите корректный адрес электронной почты.';
                document.getElementById('clientErrEmail').style.display = 'block';
                emailInput.classList.add('input-error');
                hasError = true;
            }

            // Проверка пароля
            if (!validatePasswordLive()) {
                document.getElementById('clientErrPass').textContent = 'Пароль должен содержать минимум 8 символов, включая буквы и цифры.';
                document.getElementById('clientErrPass').style.display = 'block';
                passInput.classList.add('input-error');

                if (passInput.value !== passConfirm.value) {
                    document.getElementById('clientErrPassConfirm').textContent = 'Пароли не совпадают.';
                    document.getElementById('clientErrPassConfirm').style.display = 'block';
                    passConfirm.classList.add('input-error');
                }
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
