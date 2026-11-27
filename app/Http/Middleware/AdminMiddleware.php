<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Обработка входящего запроса с проверкой прав администратора (Требование 6)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Если пользователь не авторизован (гость)
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Для доступа к панели администратора необходимо сначала войти в систему.');
        }

        // 6. Если авторизован, но не имеет прав администратора — редирект на главную страницу с сообщением об ошибке
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Доступ ограничен! У вашей учетной записи нет прав администратора для входа в панель управления.');
        }

        return $next($request);
    }
}
