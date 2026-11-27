<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Seat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FlightController extends Controller
{

    public function index(Request $request)
    {
        $departureCity = $request->input('from');
        $arrivalCity = $request->input('to');
        $flightDate = $request->input('date');
        $priceSort = $request->input('price_sort', 'asc');
        $timeFilter = $request->input('time_filter');

        $query = Flight::availableForBooking();

        if (!empty($departureCity)) {
            $query->where('departure_city', 'LIKE', "%{$departureCity}%");
        }

        if (!empty($arrivalCity)) {
            $query->where('arrival_city', 'LIKE', "%{$arrivalCity}%");
        }

        if (!empty($flightDate)) {
            $query->whereDate('departure_time', $flightDate);
        }

        if (!empty($timeFilter)) {
            if ($timeFilter === 'morning') {

                $query->whereTime('departure_time', '>=', '06:00:00')
                      ->whereTime('departure_time', '<=', '11:59:59');
            } elseif ($timeFilter === 'day') {

                $query->whereTime('departure_time', '>=', '12:00:00')
                      ->whereTime('departure_time', '<=', '17:59:59');
            } elseif ($timeFilter === 'evening') {

                $query->whereTime('departure_time', '>=', '18:00:00')
                      ->whereTime('departure_time', '<=', '23:59:59');
            }
        }

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

        if ($priceSort === 'desc') {
            $query->orderByDesc('price');
        } else {
            $query->orderBy('price', 'asc');
        }

        $flights = $query->get();

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

    public function getSeats(Flight $flight)
    {

        $seats = $flight->seats()->orderBy('id')->get();

        $occupiedSeatIds = $flight->bookings()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->pluck('seat_id')
            ->toArray();

        $seatsData = $seats->map(function (Seat $seat) use ($occupiedSeatIds, $flight) {
            $isOccupied = in_array($seat->id, $occupiedSeatIds);

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
