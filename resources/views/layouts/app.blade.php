<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('aether_theme') || 'google-light';
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
</script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aether Airways — Будущее авиаперелётов в ваших руках')</title>

    <link rel="stylesheet" href="{{ asset('style/base/variables.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('style/base/common.css') }}?v=1">
    @yield('styles')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700;800&family=Onest:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="ag-announcement-bar" id="announcementBar">
        <div class="ag-announcement-content">
            <span class="ag-pill-badge-black">AETHER AIRWAYS</span>
            <span>Фиксация стоимости любого выбранного рейса на 24 часа + 1 000 Welcome Miles новым пассажирам</span>
            <a href="{{ route('flights.index') }}" class="ag-announcement-link">
                Найти рейс
                <svg class="ag-arrow-icon arrow-sm" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
        <button type="button" class="ag-announcement-close" aria-label="Закрыть">×</button>
    </div>

    @include('components.header', ['active' => $activePage ?? 'home'])

    <div id="toastContainer" class="toast-container" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;">
        @if(session('success'))
            <div class="toast-card toast-success" style="background: #10b981; color: #fff; padding: 14px 20px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="toast-card toast-error" style="background: #ef4444; color: #fff; padding: 14px 20px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @elseif($errors->any())
            <div class="toast-card toast-error" style="background: #ef4444; color: #fff; padding: 14px 20px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <span>✕</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="toast-card toast-info" style="background: #3b82f6; color: #fff; padding: 14px 20px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <span>ℹ</span>
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>

    @yield('content')

    @include('components.footer')

    <div class="modal-backdrop" id="themeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Палитра оформления Aether Airways</h3>
                    <span class="text-secondary modal-header-sub">Выберите комфортный стиль отображения интерфейса</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeThemeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="theme-options-grid">
                    <div class="theme-option-card" data-theme-key="google-light">
                        <div class="theme-card-preview-bar">
                            <span class="preview-dot dot-blue"></span>
                            <span class="preview-dot dot-green"></span>
                            <span class="preview-dot dot-red"></span>
                        </div>
                        <h4 class="theme-card-title">Google Светлая</h4>
                        <p class="theme-card-desc">Чистый фирменный интерфейс с синим акцентом.</p>
                    </div>

                    <div class="theme-option-card" data-theme-key="indigo">
                        <div class="theme-card-preview-bar">
                            <span class="preview-dot dot-indigo-1"></span>
                            <span class="preview-dot dot-indigo-2"></span>
                            <span class="preview-dot dot-indigo-3"></span>
                        </div>
                        <h4 class="theme-card-title">Индиго стратосфера</h4>
                        <p class="theme-card-desc">Глубокий ультрамарин высоты ночного полёта.</p>
                    </div>

                    <div class="theme-option-card" data-theme-key="rose">
                        <div class="theme-card-preview-bar">
                            <span class="preview-dot dot-rose-1"></span>
                            <span class="preview-dot dot-rose-2"></span>
                            <span class="preview-dot dot-rose-3"></span>
                        </div>
                        <h4 class="theme-card-title">Коралловый закат</h4>
                        <p class="theme-card-desc">Тёплый розовато-коралловый оттенок предзакатного неба.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeThemeModal()">Готово</button>
            </div>
        </div>
    </div>

    @yield('modals')

    <script src="{{ asset('js/main.js') }}?v=5"></script>
    <script>
        setTimeout(() => {
            const toasts = document.querySelectorAll('#toastContainer .toast-card');
            toasts.forEach(t => {
                t.style.transition = 'opacity 0.5s ease';
                t.style.opacity = '0';
                setTimeout(() => t.remove(), 500);
            });
        }, 5000);
</script>
    @yield('scripts')
</body>
</html>
