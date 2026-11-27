<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Seat;
use App\Models\MileTransaction;
use App\Models\PetBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    /**
     * Создание бронирования на 24 часа (Функция 7)
     */
    public function store(Request $request)
    {
        // 1. Проверяем, что пользователь авторизован
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Для оформления бронирования необходимо войти в систему или зарегистрироваться.');
        }

        $user = Auth::user();

        // 2. Проверяем наличие необходимых паспортных данных
        if (!$user->hasCompletePassport()) {
            return redirect()->route('profile', ['tab' => 'tabPassport'])
                ->with('error', 'Перед созданием бронирования необходимо заполнить паспортные данные в личном кабинете.');
        }

        $hasPet = $request->boolean('has_pet');
        $rules = [
            'flight_id' => ['required', 'exists:flights,id'],
            'seat_id' => ['required', 'exists:seats,id'],
        ];

        if ($hasPet) {
            $rules['pet_transport_type'] = ['required', 'in:cabin,seat,hold,guide'];
            $rules['pet_type'] = ['required', 'in:dog,cat,bird,other'];
            $rules['pet_name'] = ['required', 'string', 'max:100'];
            $rules['pet_breed'] = ['nullable', 'string', 'max:100'];
            $rules['pet_weight'] = ['required', 'numeric', 'min:0.1', 'max:50'];
            $rules['pet_carrier_dimensions'] = ['nullable', 'string', 'max:50'];
            $rules['pet_vet_passport_number'] = ['required', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        $flight = Flight::findOrFail($validated['flight_id']);
        $seat = Seat::findOrFail($validated['seat_id']);

        // 3. Проверяем соответствие выбранного места выбранному рейсу
        if ($seat->flight_id !== $flight->id) {
            return back()->with('error', 'Выбранное место не принадлежит данному рейсу.');
        }

        // Проверяем авиационные правила перевозки животных
        if ($hasPet) {
            $transportType = $validated['pet_transport_type'];
            $weight = (float)$validated['pet_weight'];

            // Ограничение веса: в салоне под креслом не более 8 кг
            if ($transportType === 'cabin' && $weight > 8.0) {
                return back()->withInput()->with('error', 'Для перевозки питомца в салоне под креслом вес с переноской не должен превышать 8 кг.');
            }

            // Ограничение веса: на отдельном кресле не более 15 кг
            if ($transportType === 'seat' && $weight > 15.0) {
                return back()->withInput()->with('error', 'Для размещения питомца на отдельном кресле вес контейнера не должен превышать 15 кг.');
            }

            // Запрет аварийных рядов (ряды 7-8 на A320)
            $seatRow = (int)preg_replace('/\D/', '', $seat->seat_number);
            if (in_array($seatRow, [7, 8])) {
                return back()->withInput()->with('error', 'По правилам авиационной безопасности размещение пассажиров с животными в рядах у аварийного выхода (ряды 7-8) строго запрещено.');
            }

            // На отдельном кресле — только места у иллюминатора (A или D/F)
            if ($transportType === 'seat') {
                $seatLetter = strtoupper(preg_replace('/[^a-zA-Z]/', '', $seat->seat_number));
                if (!in_array($seatLetter, ['A', 'D', 'F'])) {
                    return back()->withInput()->with('error', 'При перевозке питомца на отдельном кресле разрешены только места у иллюминатора (A, D или F).');
                }
            }
        }

        // Проверяем, доступен ли рейс
        if ($flight->status !== 'scheduled' || $flight->departure_time <= now()) {
            return back()->with('error', 'Данный рейс недоступен для бронирования.');
        }

        // 4. Проверяем, что место свободно в рамках транзакции
        return DB::transaction(function () use ($user, $flight, $seat, $hasPet, $validated) {
            // Проверяем занятость: status = active И expires_at > now
            $isOccupied = Booking::where('seat_id', $seat->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->exists();

            if ($isOccupied) {
                return back()->with('error', 'Выбранное место уже занято другим пассажиром. Пожалуйста, выберите другое место.');
            }

            // Проверка квот на перевозку животных
            if ($hasPet) {
                $transportType = $validated['pet_transport_type'];
                if (in_array($transportType, ['cabin', 'seat'])) {
                    $cabinLimit = $flight->pet_cabin_quota ?? 4;
                    if ($flight->availablePetCabinQuota() <= 0) {
                        return back()->with('error', "Квота на перевозку животных в салоне на данном рейсе исчерпана (максимум {$cabinLimit} животных).");
                    }
                } elseif ($transportType === 'hold') {
                    $holdLimit = $flight->pet_hold_quota ?? 3;
                    if ($flight->availablePetHoldQuota() <= 0) {
                        return back()->with('error', "Квота на перевозку животных в багажном отсеке на данном рейсе исчерпана (максимум {$holdLimit} клеток).");
                    }
                }
            }

            // 5. Создаём уникальный номер бронирования (BK-XXXXX)
            do {
                $bookingNumber = 'BK-' . strtoupper(Str::random(5));
            } while (Booking::where('booking_number', $bookingNumber)->exists());

            // 6. Фиксируем стоимость билета с учетом класса и питомца
            $extraPrice = 0;
            if ($seat->seat_class === 'comfort') {
                $extraPrice = 2000;
            } elseif ($seat->seat_class === 'business') {
                $extraPrice = 6000;
            }

            $petFee = 0;
            if ($hasPet) {
                $transportType = $validated['pet_transport_type'];
                if ($transportType === 'cabin') {
                    $petFee = 2500;
                } elseif ($transportType === 'hold') {
                    $petFee = 4000;
                } elseif ($transportType === 'seat') {
                    $petFee = $flight->price; // Оплата второго кресла
                }
            }

            $finalPrice = $flight->price + $extraPrice + $petFee;

            // 7. Создаём бронирование со сроком действия 24 часа и статусом active
            $booking = Booking::create([
                'user_id' => $user->id,
                'flight_id' => $flight->id,
                'seat_id' => $seat->id,
                'booking_number' => $bookingNumber,
                'booking_class' => $seat->seat_class,
                'price' => $finalPrice,
                'passenger_name' => $user->name,
                'passenger_surname' => $user->surname,
                'passport_series' => $user->passport_series,
                'passport_number' => $user->passport_number,
                'status' => 'active',
                'expires_at' => Carbon::now()->addHours(24),
                'extension_count' => 0,
                'receipt_path' => null,
            ]);

            // 8. Фиксируем услугу перевозки питомца
            if ($hasPet) {
                PetBooking::create([
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'transport_type' => $validated['pet_transport_type'],
                    'pet_type' => $validated['pet_type'],
                    'pet_name' => $validated['pet_name'],
                    'breed' => $validated['pet_breed'] ?? null,
                    'weight' => $validated['pet_weight'],
                    'carrier_dimensions' => $validated['pet_carrier_dimensions'] ?? '44x30x26',
                    'vet_passport_number' => $validated['pet_vet_passport_number'],
                    'rabies_vaccinated' => true,
                    'service_fee' => $petFee,
                    'status' => 'confirmed',
                ]);
            }

            $successMsg = $hasPet
                ? "Бронирование {$booking->booking_number} с питомцем ({$validated['pet_name']}) успешно создано! Тариф зафиксирован на 24 часа."
                : "Бронирование {$booking->booking_number} успешно создано! Тариф зафиксирован на 24 часа.";

            return redirect()->route('profile')->with('success', $successMsg);
        });
    }

    /**
     * Апгрейд класса обслуживания за бонусные мили (Функция 9)
     * Economy -> Comfort = 2000 миль
     */
    public function upgrade(Booking $booking)
    {
        $user = Auth::user();

        // Проверка владельца бронирования
        if ($booking->user_id !== $user->id) {
            abort(403, 'Доступ к чужому бронированию запрещен.');
        }

        // Проверка активности и срока
        $booking->syncExpiredStatus();
        if ($booking->status !== 'active' || $booking->expires_at <= now()) {
            return back()->with('error', 'Апгрейд возможен только для активных бронирований с не истекшим сроком.');
        }

        // Проверка текущего класса (только economy)
        if ($booking->booking_class !== 'economy') {
            return back()->with('error', 'Апгрейд возможен только для бронирований эконом-класса.');
        }

        $upgradeCost = 2000; // Фиксированная стоимость апгрейда

        // Проверка баланса миль
        if ($user->miles < $upgradeCost) {
            return back()->with('error', "Недостаточно миль. Требуется {$upgradeCost} миль, ваш баланс: {$user->miles}.");
        }

        // Ищем свободное место класса comfort на этом рейсе
        $occupiedSeatIds = Booking::where('flight_id', $booking->flight_id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->pluck('seat_id')
            ->toArray();

        $comfortSeat = Seat::where('flight_id', $booking->flight_id)
            ->where('seat_class', 'comfort')
            ->whereNotIn('id', $occupiedSeatIds)
            ->first();

        if (!$comfortSeat) {
            return back()->with('error', 'К сожалению, на данном рейсе больше нет свободных мест класса Комфорт.');
        }

        // Выполняем все изменения в одной атомарной транзакции БД
        DB::transaction(function () use ($user, $booking, $comfortSeat, $upgradeCost) {
            // Списываем мили
            $user->decrement('miles', $upgradeCost);

            // Меняем класс бронирования и переназначаем реальное место
            $booking->update([
                'booking_class' => 'comfort',
                'seat_id' => $comfortSeat->id,
            ]);

            // Фиксируем операцию в истории миль
            MileTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => 'upgrade',
                'amount' => -$upgradeCost,
                'description' => "Апгрейд класса обслуживания Эконом → Комфорт (бронь {$booking->booking_number}, место {$comfortSeat->seat_number})",
            ]);
        });

        return back()->with('success', "Класс бронирования повышен до Комфорт! Место изменено на {$comfortSeat->seat_number}. Списано {$upgradeCost} миль.");
    }

    /**
     * Продление активного бронирования за бонусные мили (Функция 10)
     * +24 часа за 500 миль (не более 1 раза)
     */
    public function extend(Booking $booking)
    {
        $user = Auth::user();

        // Проверка владельца бронирования
        if ($booking->user_id !== $user->id) {
            abort(403, 'Доступ к чужому бронированию запрещен.');
        }

        // Проверка активности и срока
        $booking->syncExpiredStatus();
        if ($booking->status !== 'active' || $booking->expires_at <= now()) {
            return back()->with('error', 'Продление возможно только для активного бронирования, срок которого еще не истек.');
        }

        // Ограничение: одно продление для одного бронирования
        if ($booking->extension_count >= 1) {
            return back()->with('error', 'Данное бронирование уже было продлено максимально допустимое число раз (1/1).');
        }

        $extensionCost = 500; // Фиксированная стоимость продления

        // Проверка баланса миль
        if ($user->miles < $extensionCost) {
            return back()->with('error', "Недостаточно бонусных миль. Стоимость продления: {$extensionCost} миль, ваш баланс: {$user->miles}.");
        }

        // Выполняем в транзакции БД
        DB::transaction(function () use ($user, $booking, $extensionCost) {
            // Списываем мили
            $user->decrement('miles', $extensionCost);

            // Увеличиваем срок действия на 24 часа и счетчик продлений
            $newExpiresAt = Carbon::parse($booking->expires_at)->addHours(24);
            $booking->update([
                'expires_at' => $newExpiresAt,
                'extension_count' => $booking->extension_count + 1,
            ]);

            // Фиксируем операцию в истории миль
            MileTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'type' => 'extension',
                'amount' => -$extensionCost,
                'description' => "Продление срока действия бронирования {$booking->booking_number} на 24 часа",
            ]);
        });

        return back()->with('success', "Бронирование успешно продлено на 24 часа! Списано {$extensionCost} миль.");
    }

    /**
     * Отмена активного бронирования пассажиром (Функция 3)
     */
    public function cancel(Booking $booking)
    {
        $user = Auth::user();

        // Проверка владельца бронирования
        if ($booking->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Вы можете отменять только собственные бронирования.');
        }

        // Проверка активности бронирования
        $booking->syncExpiredStatus();
        if ($booking->status !== 'active') {
            return back()->with('error', 'Отменить можно только активное бронирование.');
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', "Бронирование {$booking->booking_number} успешно отменено. Место освобождено для других пассажиров.");
    }

    /**
     * Генерация и скачивание PDF-квитанции (Функция 13)
     */
    public function downloadReceipt(Booking $booking)
    {
        $user = Auth::user();

        // Система обязательно проверяет, что пользователь получает квитанцию только своего бронирования
        if ($booking->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Вы можете скачивать квитанции только собственных бронирований.');
        }

        $booking->load(['flight', 'seat', 'user']);

        // Формируем PDF с помощью barryvdh/laravel-dompdf
        $pdf = Pdf::loadView('receipts.pdf', compact('booking'))
                  ->setPaper('a4', 'portrait');

        // Сохраняем в локальное хранилище storage
        $fileName = "receipt_{$booking->booking_number}.pdf";
        $directory = 'receipts';
        
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $storagePath = "{$directory}/{$fileName}";
        Storage::disk('public')->put($storagePath, $pdf->output());

        // Записываем путь в таблицу bookings
        $booking->update(['receipt_path' => "storage/{$storagePath}"]);

        return $pdf->download("Aether_Airways_{$booking->booking_number}.pdf");
    }

    /**
     * Просмотр квитанции в браузере (Stream)
     */
    public function viewReceipt(Booking $booking)
    {
        $user = Auth::user();

        if ($booking->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Вы можете просматривать квитанции только собственных бронирований.');
        }

        $booking->load(['flight', 'seat', 'user']);

        $pdf = Pdf::loadView('receipts.pdf', compact('booking'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("Aether_Airways_{$booking->booking_number}.pdf");
    }
}
