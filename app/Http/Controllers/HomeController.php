<?php

namespace App\Http\Controllers;

use App\Models\SpecialOffer;
use App\Models\Flight;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Главная страница: отображение TOP-3 специальных предложений (Функция 5)
     */
    public function index()
    {
        // 5. Отображение TOP-3 специальных предложений с наибольшей скидкой
        $topOffers = SpecialOffer::with('flight')
            ->active()
            ->orderByDesc('discount')
            ->take(3)
            ->get();

        // Уникальные города для автокомплита формы поиска (Функция 4)
        $departureCities = Flight::select('departure_city')->distinct()->pluck('departure_city');
        $arrivalCities = Flight::select('arrival_city')->distinct()->pluck('arrival_city');

        return view('home', compact('topOffers', 'departureCities', 'arrivalCities'));
    }

    /**
     * Страница правил перевозки домашних животных
     */
    public function animals()
    {
        return view('animals');
    }
}
