<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Booking;
use App\Models\User;
use App\Models\MileTransaction;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_flights' => Flight::count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
        ];

        $flights = Flight::withCount(['seats', 'bookings'])->orderByDesc('departure_time')->get();
        $recentBookings = Booking::with(['user', 'flight', 'seat'])->latest()->take(10)->get();

        return view('admin.index', compact('stats', 'flights', 'recentBookings'));
    }

    public function completeFlight(Flight $flight)
    {
        if ($flight->status === 'completed') {
            return back()->with('info', 'Данный рейс уже имеет статус «Завершён».');
        }

        DB::transaction(function () use ($flight) {

            $flight->update(['status' => 'completed']);

            $activeBookings = Booking::where('flight_id', $flight->id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            $accruedCount = 0;

            foreach ($activeBookings as $booking) {

                $booking->update(['status' => 'completed']);

                $alreadyAccrued = MileTransaction::where('booking_id', $booking->id)
                    ->where('type', 'flight')
                    ->exists();

                if (!$alreadyAccrued) {

                    $earnedMiles = max(1000, (int)($booking->price * 0.2));

                    $booking->user->increment('miles', $earnedMiles);

                    MileTransaction::create([
                        'user_id' => $booking->user_id,
                        'booking_id' => $booking->id,
                        'type' => 'flight',
                        'amount' => $earnedMiles,
                        'description' => "Начисление бонусных миль за совершённый рейс {$flight->flight_number} ({$flight->departure_city} — {$flight->arrival_city})",
                    ]);

                    $accruedCount++;
                }
            }
        });

        return back()->with('success', "Рейс {$flight->flight_number} успешно завершён! Пассажирам активных бронирований начислены бонусные мили.");
    }

    public function updateFlightStatus(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:scheduled,completed,cancelled'],
        ]);

        if ($validated['status'] === 'completed') {
            return $this->completeFlight($flight);
        }

        $flight->update(['status' => $validated['status']]);

        return back()->with('success', "Статус рейса {$flight->flight_number} изменен на: {$validated['status']}");
    }
}
