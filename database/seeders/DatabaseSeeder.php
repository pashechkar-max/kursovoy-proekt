<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Flight;
use App\Models\Seat;
use App\Models\SpecialOffer;
use App\Models\Booking;
use App\Models\MileTransaction;
use App\Models\PetBooking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ПОЛЬЗОВАТЕЛИ
        // Администратор системы
        $admin = User::create([
            'name' => 'Главный',
            'surname' => 'Администратор',
            'email' => 'admin@aether.local',
            'phone' => '+7 (999) 000-00-01',
            'birth_date' => '1990-01-01',
            'passport_series' => '4010',
            'passport_number' => '111222',
            'passport_issued_date' => '2015-05-10',
            'passport_issued_by' => 'ГУ МВД по г. Москва',
            'citizenship' => 'Российская Федерация',
            'miles' => 50000,
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Основной пользователь для демонстрации (соответствует шаблону profile.html)
        $user = User::create([
            'name' => 'Александра',
            'surname' => 'Александрова',
            'email' => 'hello.alex@gmail.com',
            'phone' => '+7 (913) 456-78-90',
            'birth_date' => '1998-07-15',
            'passport_series' => '6914',
            'passport_number' => '482010',
            'passport_issued_date' => '2018-08-20',
            'passport_issued_by' => 'ТП УФМС России по Томской области в Кировском р-не',
            'citizenship' => 'Российская Федерация',
            'miles' => 3500,
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        // Транзакции миль для Александры (1000 welcome + 2500 flight = 3500)
        MileTransaction::create([
            'user_id' => $user->id,
            'type' => 'welcome',
            'amount' => 1000,
            'description' => 'Приветственные мили за регистрацию (Welcome Miles)',
            'created_at' => Carbon::now()->subDays(10),
        ]);

        MileTransaction::create([
            'user_id' => $user->id,
            'type' => 'flight',
            'amount' => 2500,
            'description' => 'Начисление за совершенный рейс SU-104 (Москва — Сочи)',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        // Дополнительный пользователь с незаполненным паспортом
        $newUser = User::create([
            'name' => 'Иван',
            'surname' => 'Новичков',
            'email' => 'ivan@gmail.com',
            'phone' => '+7 (905) 123-45-67',
            'birth_date' => null,
            'passport_series' => null,
            'passport_number' => null,
            'passport_issued_date' => null,
            'passport_issued_by' => null,
            'citizenship' => null,
            'miles' => 1000,
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        MileTransaction::create([
            'user_id' => $newUser->id,
            'type' => 'welcome',
            'amount' => 1000,
            'description' => 'Приветственные мили за регистрацию (Welcome Miles)',
            'created_at' => Carbon::now()->subDay(),
        ]);

        // 2. АВИАРЕЙСЫ
        $now = Carbon::now();

        $flightsData = [
            // Рейс 1: Новосибирск -> Сочи (Утренний)
            [
                'flight_number' => 'KS-501',
                'departure_city' => 'Новосибирск (OVB)',
                'arrival_city' => 'Сочи (AER)',
                'departure_time' => (clone $now)->addDays(5)->setTime(7, 30),
                'arrival_time' => (clone $now)->addDays(5)->setTime(12, 10),
                'price' => 12500.00,
                'status' => 'scheduled',
            ],
            // Рейс 2: Новосибирск -> Сочи (Дневной)
            [
                'flight_number' => 'SU-114',
                'departure_city' => 'Новосибирск (OVB)',
                'arrival_city' => 'Сочи (AER)',
                'departure_time' => (clone $now)->addDays(5)->setTime(14, 15),
                'arrival_time' => (clone $now)->addDays(5)->setTime(18, 55),
                'price' => 14500.00,
                'status' => 'scheduled',
            ],
            // Рейс 3: Новосибирск -> Сочи (Вечерний)
            [
                'flight_number' => 'SU-118',
                'departure_city' => 'Новосибирск (OVB)',
                'arrival_city' => 'Сочи (AER)',
                'departure_time' => (clone $now)->addDays(5)->setTime(20, 45),
                'arrival_time' => (clone $now)->addDays(6)->setTime(1, 20),
                'price' => 11200.00,
                'status' => 'scheduled',
            ],
            // Рейс 4: Томск -> Москва (Утренний - рейс из квитанции BK-89421)
            [
                'flight_number' => 'IW-1827',
                'departure_city' => 'Томск (TOF)',
                'arrival_city' => 'Москва (SVO)',
                'departure_time' => (clone $now)->addDays(7)->setTime(7, 40),
                'arrival_time' => (clone $now)->addDays(7)->setTime(11, 55),
                'price' => 9955.00,
                'status' => 'scheduled',
            ],
            // Рейс 5: Москва -> Санкт-Петербург (Утренний)
            [
                'flight_number' => 'SU-022',
                'departure_city' => 'Москва (SVO)',
                'arrival_city' => 'Санкт-Петербург (LED)',
                'departure_time' => (clone $now)->addDays(4)->setTime(9, 00),
                'arrival_time' => (clone $now)->addDays(4)->setTime(10, 30),
                'price' => 4500.00,
                'status' => 'scheduled',
            ],
            // Рейс 6: Москва -> Сочи (Дневной)
            [
                'flight_number' => 'SU-102',
                'departure_city' => 'Москва (SVO)',
                'arrival_city' => 'Сочи (AER)',
                'departure_time' => (clone $now)->addDays(6)->setTime(13, 10),
                'arrival_time' => (clone $now)->addDays(6)->setTime(17, 30),
                'price' => 8900.00,
                'status' => 'scheduled',
            ],
            // Рейс 7: Казань -> Москва (Вечерний)
            [
                'flight_number' => 'RT-440',
                'departure_city' => 'Казань (KZN)',
                'arrival_city' => 'Москва (SVO)',
                'departure_time' => (clone $now)->addDays(3)->setTime(19, 20),
                'arrival_time' => (clone $now)->addDays(3)->setTime(20, 50),
                'price' => 5200.00,
                'status' => 'scheduled',
            ],
            // Рейс 8: Сочи -> Москва (Прошедший, для демонстрации начисления миль за завершенный рейс)
            [
                'flight_number' => 'SU-104',
                'departure_city' => 'Сочи (AER)',
                'arrival_city' => 'Москва (SVO)',
                'departure_time' => (clone $now)->subDays(3)->setTime(11, 00),
                'arrival_time' => (clone $now)->subDays(3)->setTime(15, 20),
                'price' => 7800.00,
                'status' => 'completed',
            ],
        ];

        $createdFlights = [];
        foreach ($flightsData as $fData) {
            $flight = Flight::create($fData);
            $createdFlights[$flight->flight_number] = $flight;

            // Генерация кресел (Business, Comfort, Economy)
            // Бизнес: 1A, 1B, 1C, 1D, 2A, 2B, 2C, 2D
            foreach ([1, 2] as $row) {
                foreach (['A', 'B', 'C', 'D'] as $letter) {
                    Seat::create([
                        'flight_id' => $flight->id,
                        'seat_number' => "{$row}{$letter}",
                        'seat_class' => 'business',
                    ]);
                }
            }

            // Комфорт: 3A, 3B, 3C, 3D, 4A, 4B, 4C, 4D
            foreach ([3, 4] as $row) {
                foreach (['A', 'B', 'C', 'D'] as $letter) {
                    Seat::create([
                        'flight_id' => $flight->id,
                        'seat_number' => "{$row}{$letter}",
                        'seat_class' => 'comfort',
                    ]);
                }
            }

            // Эконом: 5A-5F .. 15A-15F
            foreach (range(5, 15) as $row) {
                foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter) {
                    Seat::create([
                        'flight_id' => $flight->id,
                        'seat_number' => "{$row}{$letter}",
                        'seat_class' => 'economy',
                    ]);
                }
            }
        }

        // 3. СПЕЦИАЛЬНЫЕ ПРЕДЛОЖЕНИЯ (не менее 10, с разными скидками)
        $offersData = [
            [
                'flight_id' => $createdFlights['SU-114']->id,
                'title' => 'Бархатный сезон в Сочи — субсидированный тариф',
                'description' => 'Прямые рейсы на Черноморское побережье с фиксированной ценой и бесплатным провозом багажа.',
                'image' => 'image/offer_sochi.jpg',
                'discount' => 35.00, // Наибольшая скидка (ТОП-1)
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(30),
            ],
            [
                'flight_id' => $createdFlights['SU-022']->id,
                'title' => 'Северная столица: выходные в Санкт-Петербурге',
                'description' => 'Эрмитаж, мосты и театры. Специальные утренние слоты вылета из Москвы.',
                'image' => 'image/offer_spb.jpg',
                'discount' => 30.00, // ТОП-2
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(25),
            ],
            [
                'flight_id' => $createdFlights['KS-501']->id,
                'title' => 'Зимние каникулы в горах Красной Поляны',
                'description' => 'Удобная доставка горнолыжного оборудования без дополнительной платы.',
                'image' => 'image/offer_baikal.jpg',
                'discount' => 25.00, // ТОП-3
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(20),
            ],
            [
                'flight_id' => $createdFlights['RT-440']->id,
                'title' => 'Казань гастрономическая — чак-чак и кремль',
                'description' => 'Погрузитесь в атмосферу восточного гостеприимства в сердце Татарстана.',
                'image' => 'image/offer_altai.jpg',
                'discount' => 20.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(15),
            ],
            [
                'flight_id' => $createdFlights['IW-1827']->id,
                'title' => 'Университетский Томск — старинное деревянное зодчество',
                'description' => 'Специальные тарифы для студентов и преподавателей.',
                'image' => 'image/offer_sochi.jpg',
                'discount' => 18.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(4),
                'end_date' => Carbon::now()->addDays(14),
            ],
            [
                'flight_id' => $createdFlights['SU-102']->id,
                'title' => 'Морской бриз и солнце Адлера',
                'description' => 'Быстрый перелет к морю на комфортабельном лайнере Airbus A320neo.',
                'image' => 'image/offer_spb.jpg',
                'discount' => 15.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(40),
            ],
            [
                'flight_id' => $createdFlights['SU-118']->id,
                'title' => 'Ночной рейс Новосибирск — Сочи',
                'description' => 'Прибывайте отдохнувшими и готовыми к новому дню на курорте.',
                'image' => 'image/offer_baikal.jpg',
                'discount' => 12.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(18),
            ],
            [
                'flight_id' => $createdFlights['KS-501']->id,
                'title' => 'Семейное путешествие с детьми',
                'description' => 'Скидки на детские билеты и приоритетная посадка.',
                'image' => 'image/offer_altai.jpg',
                'discount' => 10.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(8),
                'end_date' => Carbon::now()->addDays(22),
            ],
            [
                'flight_id' => $createdFlights['SU-022']->id,
                'title' => 'Бизнес-шаттл: утро в Москве, вечер на Неве',
                'description' => 'Идеально для командировок и деловых встреч с гарантией пунктуальности.',
                'image' => 'image/offer_sochi.jpg',
                'discount' => 8.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(6),
                'end_date' => Carbon::now()->addDays(12),
            ],
            [
                'flight_id' => $createdFlights['RT-440']->id,
                'title' => 'Раннее бронирование авиабилетов',
                'description' => 'Планируйте поездки заранее и экономьте на базовом тарифе.',
                'image' => 'image/offer_spb.jpg',
                'discount' => 5.00,
                'is_active' => true,
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(50),
            ],
        ];

        foreach ($offersData as $oData) {
            SpecialOffer::create($oData);
        }

        // 4. ТЕСТОВЫЕ БРОНИРОВАНИЯ ДЛЯ ПОЛЬЗОВАТЕЛЯ
        // Бронирование 1: Активное бронирование на рейс IW-1827 (Томск -> Москва)
        // Место 12A (Economy) - доступно для апгрейда и продления!
        $flightIW = $createdFlights['IW-1827'];
        $seat12A = Seat::where('flight_id', $flightIW->id)->where('seat_number', '12A')->first();

        $booking1 = Booking::create([
            'user_id' => $user->id,
            'flight_id' => $flightIW->id,
            'seat_id' => $seat12A->id,
            'booking_number' => 'BK-89421',
            'booking_class' => 'economy',
            'price' => 9955.00,
            'passenger_name' => $user->name,
            'passenger_surname' => $user->surname,
            'passport_series' => $user->passport_series,
            'passport_number' => $user->passport_number,
            'status' => 'active',
            'expires_at' => Carbon::now()->addHours(23),
            'extension_count' => 0,
            'receipt_path' => null,
        ]);

        // Бронирование 2: Прошедшее завершенное бронирование на рейс SU-104
        $flightSU104 = $createdFlights['SU-104'];
        $seat4B = Seat::where('flight_id', $flightSU104->id)->where('seat_number', '4B')->first();

        $booking2 = Booking::create([
            'user_id' => $user->id,
            'flight_id' => $flightSU104->id,
            'seat_id' => $seat4B->id,
            'booking_number' => 'BK-71203',
            'booking_class' => 'comfort',
            'price' => 7800.00,
            'passenger_name' => $user->name,
            'passenger_surname' => $user->surname,
            'passport_series' => $user->passport_series,
            'passport_number' => $user->passport_number,
            'status' => 'completed',
            'expires_at' => Carbon::now()->subDays(4),
            'extension_count' => 0,
            'receipt_path' => null,
        ]);

        PetBooking::create([
            'booking_id' => $booking2->id,
            'user_id' => $user->id,
            'transport_type' => 'cabin',
            'pet_type' => 'cat',
            'pet_name' => 'Симба',
            'breed' => 'Британская короткошерстная',
            'weight' => 4.8,
            'carrier_dimensions' => '44x30x26',
            'vet_passport_number' => 'RU-654321',
            'rabies_vaccinated' => true,
            'service_fee' => 2500,
            'status' => 'confirmed',
        ]);

        // Бронирование 3: Истёкшее бронирование (expired)
        $flightSU118 = $createdFlights['SU-118'];
        $seat7C = Seat::where('flight_id', $flightSU118->id)->where('seat_number', '7C')->first();

        Booking::create([
            'user_id' => $user->id,
            'flight_id' => $flightSU118->id,
            'seat_id' => $seat7C->id,
            'booking_number' => 'BK-65109',
            'booking_class' => 'economy',
            'price' => 11200.00,
            'passenger_name' => $user->name,
            'passenger_surname' => $user->surname,
            'passport_series' => $user->passport_series,
            'passport_number' => $user->passport_number,
            'status' => 'expired',
            'expires_at' => Carbon::now()->subHours(10),
            'extension_count' => 0,
            'receipt_path' => null,
        ]);
    }
}
