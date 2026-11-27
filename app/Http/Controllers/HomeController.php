<?php

namespace App\Http\Controllers;

use App\Models\SpecialOffer;
use App\Models\Flight;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {

        $topOffers = SpecialOffer::with('flight')
            ->active()
            ->orderByDesc('discount')
            ->take(3)
            ->get();

        $departureCities = Flight::select('departure_city')->distinct()->pluck('departure_city');
        $arrivalCities = Flight::select('arrival_city')->distinct()->pluck('arrival_city');

        return view('home', compact('topOffers', 'departureCities', 'arrivalCities'));
    }

    public function animals()
    {
        return view('animals');
    }
}
