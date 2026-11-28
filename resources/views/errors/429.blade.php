<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 — Защита от DDoS • Aether Airways</title>
    <link rel="stylesheet" href="{{ asset('public/style/errors/error.css') }}?v=1">
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
            <div class="error-visual-badge error-visual-badge-danger">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>

            <div class="error-huge-code error-huge-code-danger">429</div>
            <h1 class="error-title">Сработала защита от DDoS-атак</h1>
            <p class="error-desc">
                {{ $message ?? 'Слишком много запросов с вашего IP-адреса. Автоматическая система безопасности временно ограничила доступ для предотвращения перегрузки серверов и подбора паролей. Пожалуйста, подождите 1 минуту.' }}
            </p>

            <div class="error-actions-group">
                <a href="{{ route('home') }}" class="btn btn-primary btn-md">Вернуться на главную</a>
            </div>
        </main>

        <footer class="error-footer">
            &copy; {{ date('Y') }} Aether Airways Security System. Все права защищены.
        </footer>
    </div>
</body>
</html>
