@props(['active' => 'home'])

<header class="site-header">
    <div class="header-container">
        <div class="header-left">
            <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Открыть меню">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <a href="{{ route('home') }}" class="header-logo-link" title="Aether Airways">
                <span class="brand-text-logo">AETHER AIRWAYS</span>
            </a>

            <nav class="header-nav">
                <a href="{{ route('flights.index') }}" class="header-nav-link {{ $active === 'flights' ? 'active' : '' }}">Рейсы</a>
                <a href="{{ auth()->check() ? route('profile') : route('login') }}" class="header-nav-link {{ $active === 'profile' ? 'active' : '' }}">Мои бронирования</a>
                <a href="{{ route('home') }}#special-offers" class="header-nav-link">Спецпредложения</a>
                <a href="{{ route('animals') }}" class="header-nav-link {{ $active === 'animals' ? 'active' : '' }}">Питомцы</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.index') }}" class="header-nav-link {{ $active === 'admin' ? 'active' : '' }}" style="color: var(--primary-accent, #6366f1); font-weight: 700;">Управление рейсами</a>
                    @endif
                @endauth
            </nav>
        </div>

        <div class="header-right">
            @guest
                <a href="{{ route('login') }}" class="btn btn-secondary btn-sm header-action-btn">Войти</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm header-action-btn">Регистрация</a>
            @endguest

            @auth
                <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline-flex; align-items: center;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm header-action-btn" title="Выйти из аккаунта">Выйти</button>
                </form>

                <a href="{{ route('profile') }}" class="user-avatar-trigger" title="{{ auth()->user()->name }} — Личный кабинет" style="text-decoration: none;">
                    <div class="avatar-circle">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                </a>
            @endauth
        </div>
    </div>

    <div class="mobile-nav-drawer" id="mobileNavDrawer">
        <a href="{{ route('flights.index') }}">Поиск рейсов</a>
        <a href="{{ auth()->check() ? route('profile') : route('login') }}">Мои бронирования</a>
        <a href="{{ route('animals') }}">Перевозка животных</a>
        <a href="{{ route('home') }}#special-offers">Спецпредложения</a>
        @guest
            <a href="{{ route('login') }}">Войти в аккаунт</a>
            <a href="{{ route('register') }}">Регистрация</a>
        @else
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.index') }}" style="color: #4f46e5; font-weight: 700;">Панель администратора</a>
            @endif
            <a href="{{ route('profile') }}">Личный кабинет ({{ auth()->user()->miles }} миль)</a>
            <form action="{{ route('logout') }}" method="POST" style="padding: 10px 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 16px; font-weight: 600; cursor: pointer;">Выйти из аккаунта</button>
            </form>
        @endguest
    </div>
</header>
