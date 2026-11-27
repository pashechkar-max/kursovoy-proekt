<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {

        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Для доступа к панели администратора необходимо сначала войти в систему.');
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Доступ ограничен! У вашей учетной записи нет прав администратора для входа в панель управления.');
        }

        return $next($request);
    }
}
