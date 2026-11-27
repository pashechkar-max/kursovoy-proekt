<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Рейс или страница не найдены • Aether Airways</title>
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
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>

            <div class="error-huge-code">404</div>
            <h1 class="error-title">Маршрут не найден</h1>
            <p class="error-desc">
                Запрошенная страница или рейс не существует, был перемещён или удалён из расписания. Проверьте правильность адреса.
            </p>

            <div class="error-actions-group">
                <a href="{{ route('flights.index') }}" class="btn btn-primary btn-md">Поиск рейсов</a>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-md">На главную</a>
            </div>
        </main>

        <footer class="error-footer">
            &copy; {{ date('Y') }} Aether Airways. Все права защищены.
        </footer>
    </div>
</body>
</html>
