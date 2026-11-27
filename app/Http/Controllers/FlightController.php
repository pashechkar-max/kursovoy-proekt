<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Seat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FlightController extends Controller
{
    /**
     * Поиск и фильтрация рейсов (Функции 4 и 6)
     */
    public function index(Request $request)
    {
        $departureCity = $request->input('from');
        $arrivalCity = $request->input('to');
        $flightDate = $request->input('date');
        $priceSort = $request->input('price_sort', 'asc'); // asc / desc
        $timeFilter = $request->input('time_filter'); // morning, day, evening

        // Базовый запрос: только рейсы, доступные для бронирования (Функция 4)
        $query = Flight::availableForBooking();

        // Поиск по городу отправления (Функция 4)
        if (!empty($departureCity)) {
            $query->where('departure_city', 'LIKE', "%{$departureCity}%");
        }

        // Поиск по городу прибытия (Функция 4)
        if (!empty($arrivalCity)) {
            $query->where('arrival_city', 'LIKE', "%{$arrivalCity}%");
        }

        // Поиск по дате вылета (Функция 4)
        if (!empty($flightDate)) {
            $query->whereDate('departure_time', $flightDate);
        }

        // Фильтрация по времени вылета (Функция 6)
        if (!empty($timeFilter)) {
            if ($timeFilter === 'morning') {
                // Утренние рейсы: 06:00:00 - 11:59:59
                $query->whereTime('departure_time', '>=', '06:00:00')
                      ->whereTime('departure_time', '<=', '11:59:59');
            } elseif ($timeFilter === 'day') {
                // Дневные рейсы: 12:00:00 - 17:59:59
                $query->whereTime('departure_time', '>=', '12:00:00')
                      ->whereTime('departure_time', '<=', '17:59:59');
            } elseif ($timeFilter === 'evening') {
                // Вечерние рейсы: 18:00:00 - 23:59:59
                $query->whereTime('departure_time', '>=', '18:00:00')
                      ->whereTime('departure_time', '<=', '23:59:59');
            }
        }

        // Фильтр рейсов с доступными местами для питомцев
        $withPets = $request->boolean('with_pets');
        if ($withPets) {
            $query->where(function ($q) {
                $q->whereNull('pet_cabin_quota')
                  ->orWhere('pet_cabin_quota', '>', function ($sub) {
                      $sub->selectRaw('count(*)')
                          ->from('pet_bookings')
                          ->join('bookings', 'pet_bookings.booking_id', '=', 'bookings.id')
                          ->whereColumn('bookings.flight_id', 'flights.id')
                          ->where('bookings.status', 'active')
                          ->where('bookings.expires_at', '>', now())
                          ->whereIn('pet_bookings.transport_type', ['cabin', 'seat']);
                  });
            });
        }

        // Сортировка по стоимости: сначала дешёвые / сначала дорогие (Функция 6)
        if ($priceSort === 'desc') {
            $query->orderByDesc('price');
        } else {
            $query->orderBy('price', 'asc');
        }

        $flights = $query->get();

        // Списки городов для селектов
        $departureCities = Flight::select('departure_city')->distinct()->pluck('departure_city');
        $arrivalCities = Flight::select('arrival_city')->distinct()->pluck('arrival_city');

        return view('flights', compact(
            'flights',
            'departureCities',
            'arrivalCities',
            'departureCity',
            'arrivalCity',
            'flightDate',
            'priceSort',
            'timeFilter',
            'withPets'
        ));
    }

    /**
     * Получение карты мест рейса и статуса их занятости (Функция 8)
     */
    public function getSeats(Flight $flight)
    {
        // Получаем все места рейса
        $seats = $flight->seats()->orderBy('id')->get();

        // Получаем ID занятых мест (status = active и expires_at > now)
        $occupiedSeatIds = $flight->bookings()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->pluck('seat_id')
            ->toArray();

        $seatsData = $seats->map(function (Seat $seat) use ($occupiedSeatIds, $flight) {
            $isOccupied = in_array($seat->id, $occupiedSeatIds);

            // Доплата в зависимости от класса
            $classMultiplier = 0;
            if ($seat->seat_class === 'comfort') {
                $classMultiplier = 2000;
            } elseif ($seat->seat_class === 'business') {
                $classMultiplier = 6000;
            }
            $finalPrice = $flight->price + $classMultiplier;

            return [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_class' => $seat->seat_class,
                'is_available' => !$isOccupied,
                'price' => $finalPrice,
            ];
        });

        return response()->json([
            'flight' => [
                'id' => $flight->id,
                'flight_number' => $flight->flight_number,
                'departure_city' => $flight->departure_city,
                'arrival_city' => $flight->arrival_city,
                'departure_time' => $flight->departure_time->format('d.m.Y H:i'),
                'base_price' => $flight->price,
                'available_pet_cabin' => $flight->availablePetCabinQuota(),
                'available_pet_hold' => $flight->availablePetHoldQuota(),
                'pet_cabin_quota' => $flight->pet_cabin_quota ?? 4,
            ],
            'seats' => $seatsData,
        ]);
    }
}
