<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Booking;
use App\Models\User;
use App\Models\MileTransaction;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $users = User::withCount('bookings')->orderByDesc('id')->get();

        return view('admin.index', compact('stats', 'flights', 'recentBookings', 'users'));
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

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:user,admin'],
            'phone' => ['nullable', 'string', 'max:30'],
            'miles' => ['nullable', 'integer', 'min:0'],
            'passport_series' => ['nullable', 'string', 'max:10'],
            'passport_number' => ['nullable', 'string', 'max:20'],
            'passport_issued_date' => ['nullable', 'date'],
            'passport_issued_by' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'citizenship' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['miles'] = $validated['miles'] ?? 0;

        $user = User::create($validated);

        if ($validated['miles'] > 0) {
            MileTransaction::create([
                'user_id' => $user->id,
                'type' => 'welcome',
                'amount' => $validated['miles'],
                'description' => 'Начисление миль администратором при создании аккаунта',
            ]);
        }

        return back()->with('success', "Пользователь {$user->name} {$user->surname} успешно создан!");
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:user,admin'],
            'phone' => ['nullable', 'string', 'max:30'],
            'miles' => ['nullable', 'integer', 'min:0'],
            'passport_series' => ['nullable', 'string', 'max:10'],
            'passport_number' => ['nullable', 'string', 'max:20'],
            'passport_issued_date' => ['nullable', 'date'],
            'passport_issued_by' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'citizenship' => ['nullable', 'string', 'max:100'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Вы не можете снять права администратора со своей активной учётной записи.');
        }

        $user->update($validated);

        return back()->with('success', "Данные пользователя {$user->name} {$user->surname} успешно обновлены!");
    }

    public function toggleUserRole(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Вы не можете изменить свою собственную роль администратора.');
        }

        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        $roleTitle = $newRole === 'admin' ? 'Администратор' : 'Пассажир';
        return back()->with('success', "Роль пользователя {$user->name} {$user->surname} изменена на: {$roleTitle}");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Вы не можете удалить свою собственную учётную запись.');
        }

        $userName = "{$user->name} {$user->surname}";

        DB::transaction(function () use ($user) {
            foreach ($user->bookings as $booking) {
                $booking->petBooking()?->delete();
                $booking->delete();
            }
            $user->mileTransactions()->delete();
            $user->petBookings()->delete();
            $user->delete();
        });

        return back()->with('success', "Пользователь {$userName} успешно удалён!");
    }
}

