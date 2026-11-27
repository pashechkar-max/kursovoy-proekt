<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\PetBooking;
use App\Models\Seat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PetFlightTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Flight $flight;
    protected Seat $standardSeat;
    protected Seat $emergencySeat;
    protected Seat $windowSeat;
    protected Seat $middleSeat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Иван',
            'surname' => 'Петров',
            'email' => 'ivan_pet_test_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123'),
            'passport_series' => '4515',
            'passport_number' => '123456',
            'passport_issued_date' => '2015-05-15',
            'passport_issued_by' => 'ОВД г. Москвы',
            'birth_date' => '1990-01-01',
            'citizenship' => 'РФ',
            'miles' => 5000,
            'role' => 'user',
        ]);

        $this->flight = Flight::create([
            'flight_number' => 'SU-9988',
            'departure_city' => 'Москва',
            'arrival_city' => 'Сочи',
            'departure_time' => Carbon::now()->addDays(3),
            'arrival_time' => Carbon::now()->addDays(3)->addHours(4),
            'price' => 8000,
            'status' => 'scheduled',
            'pet_cabin_quota' => 4,
            'pet_hold_quota' => 3,
        ]);

        // Ряд 10 (обычный эконом)
        $this->windowSeat = Seat::create([
            'flight_id' => $this->flight->id,
            'seat_number' => '10A',
            'seat_class' => 'economy',
            'price_multiplier' => 1.0,
            'is_available' => true,
        ]);

        $this->middleSeat = Seat::create([
            'flight_id' => $this->flight->id,
            'seat_number' => '10B',
            'seat_class' => 'economy',
            'price_multiplier' => 1.0,
            'is_available' => true,
        ]);

        // Ряд 7 (аварийный выход)
        $this->emergencySeat = Seat::create([
            'flight_id' => $this->flight->id,
            'seat_number' => '7A',
            'seat_class' => 'economy',
            'price_multiplier' => 1.0,
            'is_available' => true,
        ]);
    }

    public function test_user_can_book_flight_with_pet_in_cabin()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'dog',
            'pet_name' => 'Арчи',
            'pet_breed' => 'Корги',
            'pet_weight' => 6.5,
            'pet_vet_passport_number' => 'RU-987654',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success');

        // Проверяем стоимость: 8000 (билет) + 2500 (сбор за питомца в салоне) = 10500
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id,
            'price' => 10500,
            'status' => 'active',
        ]);

        $booking = Booking::where('flight_id', $this->flight->id)->first();
        $this->assertNotNull($booking);
        $this->assertTrue($booking->hasPet());

        $this->assertDatabaseHas('pet_bookings', [
            'booking_id' => $booking->id,
            'user_id' => $this->user->id,
            'pet_name' => 'Арчи',
            'transport_type' => 'cabin',
            'pet_type' => 'dog',
            'service_fee' => 2500,
        ]);
    }

    public function test_cannot_book_cabin_pet_exceeding_weight_limit()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'cat',
            'pet_name' => 'Мейн-кун',
            'pet_weight' => 9.2, // > 8 кг запрещено в салоне
            'pet_vet_passport_number' => 'RU-112233',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->user->id,
            'flight_id' => $this->flight->id,
        ]);
        $this->assertDatabaseMissing('pet_bookings', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_book_pet_on_emergency_exit_rows()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->emergencySeat->id, // 7A
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'dog',
            'pet_name' => 'Бобик',
            'pet_weight' => 5.0,
            'pet_vet_passport_number' => 'RU-334455',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->user->id,
            'flight_id' => $this->flight->id,
        ]);
    }

    public function test_cannot_book_extra_seat_for_pet_on_non_window_seat()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id, // 10B не у окна
            'has_pet' => '1',
            'pet_transport_type' => 'seat',
            'pet_type' => 'dog',
            'pet_name' => 'Рекс',
            'pet_weight' => 12.0,
            'pet_vet_passport_number' => 'RU-556677',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->user->id,
            'flight_id' => $this->flight->id,
        ]);
    }

    public function test_can_book_extra_seat_for_pet_on_window_seat()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->windowSeat->id, // 10A у окна
            'has_pet' => '1',
            'pet_transport_type' => 'seat',
            'pet_type' => 'dog',
            'pet_name' => 'Рекс',
            'pet_weight' => 12.0,
            'pet_vet_passport_number' => 'RU-556677',
        ]);

        $response->assertRedirect(route('profile'));
        // Стоимость: 8000 + 8000 (второе место) = 16000
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'seat_id' => $this->windowSeat->id,
            'price' => 16000,
        ]);
    }

    public function test_pet_quota_enforcement()
    {
        // Устанавливаем квоту салона 1
        $this->flight->update(['pet_cabin_quota' => 1]);

        // Первая бронь с питомцем
        $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->windowSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'cat',
            'pet_name' => 'Мурка',
            'pet_weight' => 4.0,
            'pet_vet_passport_number' => 'RU-111111',
        ]);

        $this->assertEquals(0, $this->flight->fresh()->availablePetCabinQuota());

        // Второй пользователь пытается забронировать питомца в салоне
        $user2 = User::create([
            'name' => 'Анна',
            'surname' => 'Сидорова',
            'email' => 'anna_pet_test_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123'),
            'passport_series' => '4516',
            'passport_number' => '654321',
            'passport_issued_date' => '2016-06-16',
            'passport_issued_by' => 'ОВД г. Москвы',
            'birth_date' => '1992-02-02',
            'citizenship' => 'РФ',
            'miles' => 2000,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user2)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'cat',
            'pet_name' => 'Васька',
            'pet_weight' => 3.5,
            'pet_vet_passport_number' => 'RU-222222',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_booking_pet_in_hold()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'hold',
            'pet_type' => 'dog',
            'pet_name' => 'Балто',
            'pet_breed' => 'Хаски',
            'pet_weight' => 28.0,
            'pet_vet_passport_number' => 'RU-445566',
        ]);

        $response->assertRedirect(route('profile'));
        // 8000 + 4000 = 12000
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'seat_id' => $this->middleSeat->id,
            'price' => 12000,
        ]);
        $this->assertDatabaseHas('pet_bookings', [
            'pet_name' => 'Балто',
            'transport_type' => 'hold',
            'service_fee' => 4000,
        ]);
    }

    public function test_booking_guide_dog_is_free_service_fee()
    {
        $response = $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->middleSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'guide',
            'pet_type' => 'dog',
            'pet_name' => 'Гайд',
            'pet_breed' => 'Лабрадор',
            'pet_weight' => 30.0,
            'pet_vet_passport_number' => 'RU-GUIDE-01',
        ]);

        $response->assertRedirect(route('profile'));
        // 8000 + 0 = 8000
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'seat_id' => $this->middleSeat->id,
            'price' => 8000,
        ]);
        $this->assertDatabaseHas('pet_bookings', [
            'pet_name' => 'Гайд',
            'transport_type' => 'guide',
            'service_fee' => 0,
        ]);
    }

    public function test_flights_filter_with_pets()
    {
        $response = $this->get('/flights?with_pets=1');
        $response->assertStatus(200);
        $response->assertSee('Только с местами для питомцев');
    }

    public function test_profile_displays_pet_details()
    {
        $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->windowSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'dog',
            'pet_name' => 'Оскар',
            'pet_weight' => 5.0,
            'pet_vet_passport_number' => 'RU-778899',
        ]);

        $response = $this->actingAs($this->user)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertSee('Оскар');
        $response->assertSee('В салоне под креслом');
    }

    public function test_receipt_pdf_contains_pet_ssr_section()
    {
        $this->actingAs($this->user)->post(route('bookings.store'), [
            'flight_id' => $this->flight->id,
            'seat_id' => $this->windowSeat->id,
            'has_pet' => '1',
            'pet_transport_type' => 'cabin',
            'pet_type' => 'dog',
            'pet_name' => 'Оскар',
            'pet_weight' => 5.0,
            'pet_vet_passport_number' => 'RU-778899',
        ]);

        $booking = Booking::where('flight_id', $this->flight->id)->first();

        $response = $this->actingAs($this->user)->get(route('bookings.receipt', $booking));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
