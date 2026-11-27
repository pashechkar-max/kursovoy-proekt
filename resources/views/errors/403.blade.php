<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Доступ ограничен • Aether Airways</title>
    <link rel="stylesheet" href="{{ asset('style/errors/error.css') }}?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700;800&family=Onest:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="error-page-wrapper">
        <header class="error-page-header">
            <a href="{{ route('home') }}" class="header-logo-link">
                <span class="brand-text-logo">AETHER AIRWAYS</span>
            </a>
        </header>

        <main class="error-main-content">
            <div class="error-visual-badge">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>

            <div class="error-huge-code">403</div>
            <h1 class="error-title">Доступ ограничен</h1>
            <p class="error-desc">
                {{ $exception->getMessage() ?: 'У вас нет необходимых прав для просмотра этого раздела или действия. Войдите с соответствующим уровнем доступа.' }}
            </p>

            <div class="error-actions-group">
                <a href="{{ route('login') }}" class="btn btn-primary btn-md">Войти в аккаунт</a>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-md">На главную</a>
            </div>
        </main>

        <footer class="error-footer">
            &copy; {{ date('Y') }} Aether Airways. Все права защищены.
        </footer>
    </div>
</body>
</html>
