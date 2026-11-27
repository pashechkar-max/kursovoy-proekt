<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {

    }

    public function boot(): void
    {

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip())->response(function (Request $request, array $headers) {
                return response()->view('errors.429', [
                    'message' => 'Сработала автоматическая система защиты от DDoS-атак. Превышен общий лимит запросов в минуту.'
                ], 429, $headers);
            });
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function (Request $request, array $headers) {
                return response()->view('errors.429', [
                    'message' => 'Превышено допустимое количество попыток входа или регистрации (максимум 5 в минуту). В целях защиты от перебора паролей доступ временно заблокирован на 60 секунд.'
                ], 429, $headers);
            });
        });

        RateLimiter::for('bookings', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip())->response(function (Request $request, array $headers) {
                return response()->view('errors.429', [
                    'message' => 'Слишком много запросов к операциям бронирования. Пожалуйста, подождите минуту перед следующей попыткой.'
                ], 429, $headers);
            });
        });
    }
}
