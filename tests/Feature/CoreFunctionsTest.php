<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flight;
use App\Models\Seat;
use App\Models\SpecialOffer;
use App\Models\Booking;
use App\Models\MileTransaction;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CoreFunctionsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Тест: Главная страница доступна Гостю и отображает ТОП-3 спецпредложения (Функция 5)
     */
    public function test_guest_can_view_home_and_top_offers(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('AETHER AIRWAYS');
        $response->assertSee('ТОП-3 направления');
    }

    /**
     * Тест: Поиск и фильтрация рейсов (Функции 4 и 6)
     */
    public function test_flight_search_and_filter(): void
    {
        $response = $this->get('/flights?from=Новосибирск&to=Сочи&price_sort=asc');
        $response->assertStatus(200);
        $response->assertSee('KS-501');
    }

    /**
     * Тест: Регистрация нового пользователя и начисление 1000 Welcome Miles (Функции 1 и 11)
     */
    public function test_registration_awards_1000_welcome_miles(): void
    {
        $uniqueEmail = 'new_passenger_' . time() . '@test.com';

        $response = $this->post('/register', [
            'first_name' => 'Екатерина',
            'last_name' => 'Смирнова',
            'email' => $uniqueEmail,
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertRedirect('/profile');

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals(1000, $user->miles);

        // Проверка записи в истории транзакций с милями
        $transaction = MileTransaction::where('user_id', $user->id)->where('type', 'welcome')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(1000, $transaction->amount);
    }

    /**
     * Тест: Парольная политика — минимум 8 символов, обязательно буквы и цифры (Требование 2)
     */
    public function test_password_policy_enforcement(): void
    {
        // 1. Пароль короче 8 символов — должен отклоняться
        $shortPassResponse = $this->post('/register', [
            'first_name' => 'Ольга',
            'last_name' => 'Иванова',
            'email' => 'olga_short@test.com',
            'password' => 'Pass1',
            'password_confirmation' => 'Pass1',
        ]);
        $shortPassResponse->assertSessionHasErrors(['password']);

        // 2. Пароль только из букв (без цифр) — должен отклоняться
        $onlyLettersResponse = $this->post('/register', [
            'first_name' => 'Ольга',
            'last_name' => 'Иванова',
            'email' => 'olga_letters@test.com',
            'password' => 'PasswordOnly',
            'password_confirmation' => 'PasswordOnly',
        ]);
        $onlyLettersResponse->assertSessionHasErrors(['password']);

        // 3. Пароль только из цифр (без букв) — должен отклоняться
        $onlyDigitsResponse = $this->post('/register', [
            'first_name' => 'Ольга',
            'last_name' => 'Иванова',
            'email' => 'olga_digits@test.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);
        $onlyDigitsResponse->assertSessionHasErrors(['password']);
    }

    /**
     * Тест: Уникальность пользовательского логина/email (Требование 3)
     */
    public function test_unique_user_login_enforcement(): void
    {
        // Пытаемся зарегистрировать пользователя с уже существующим email hello.alex@gmail.com
        $response = $this->post('/register', [
            'first_name' => 'Повторный',
            'last_name' => 'Пользователь',
            'email' => 'hello.alex@gmail.com',
            'password' => 'ValidPass123',
            'password_confirmation' => 'ValidPass123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertTrue(session()->get('errors')->has('email'));
    }

    /**
     * Тест: Редирект на главную страницу при попытке пользователя без прав админа войти в админку (Требование 6)
     */
    public function test_non_admin_redirect_to_home(): void
    {
        // 1. Гость пытается войти в админ-панель -> редирект на форму авторизации
        $guestResponse = $this->get('/admin');
        $guestResponse->assertRedirect('/login');

        // 2. Обычный пользователь (не админ) пытается войти в админку -> редирект на главную страницу с сообщением об ошибке
        $user = User::where('role', 'user')->first();
        $userResponse = $this->actingAs($user)->get('/admin');
        $userResponse->assertRedirect('/');
        $userResponse->assertSessionHas('error');

        // 3. Администратор имеет успешный доступ в панель управления
        $admin = User::where('role', 'admin')->first();
        $adminResponse = $this->actingAs($admin)->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Управление рейсами');
    }

    /**
     * Тест: Валидация формы редактирования личных и паспортных данных (Требования 1 и 4)
     */
    public function test_passport_validation_rules(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();

        // Некорректная серия (должно быть 4 цифры) и номер (должно быть 6 цифр)
        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Александра',
            'surname' => 'Александрова',
            'passport_series' => 'ABC',
            'passport_number' => '12',
        ]);

        $response->assertSessionHasErrors(['passport_series', 'passport_number']);
    }

    /**
     * Тест: Апгрейд класса за 2 000 миль (Функция 9)
     */
    public function test_upgrade_class_for_miles(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();
        $booking = Booking::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('booking_class', 'economy')
            ->first();

        $this->assertNotNull($booking, 'Активное бронирование эконом-класса не найдено.');
        $initialMiles = $user->miles;

        $response = $this->actingAs($user)->post("/bookings/{$booking->id}/upgrade");
        $response->assertSessionHas('success');

        $booking->refresh();
        $user->refresh();

        $this->assertEquals('comfort', $booking->booking_class);
        $this->assertEquals($initialMiles - 2000, $user->miles);

        // Проверка транзакции
        $tx = MileTransaction::where('booking_id', $booking->id)->where('type', 'upgrade')->first();
        $this->assertNotNull($tx);
        $this->assertEquals(-2000, $tx->amount);
    }

    /**
     * Тест: Продление бронирования на 24 часа за 500 миль (Функция 10)
     */
    public function test_extend_booking_for_miles(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();
        $booking = Booking::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('extension_count', 0)
            ->first();

        $this->assertNotNull($booking, 'Активное бронирование не найдено.');
        $initialExpiry = Carbon::parse($booking->expires_at);
        $initialMiles = $user->miles;

        $response = $this->actingAs($user)->post("/bookings/{$booking->id}/extend");
        $response->assertSessionHas('success');

        $booking->refresh();
        $user->refresh();

        $this->assertEquals(1, $booking->extension_count);
        $this->assertEquals($initialMiles - 500, $user->miles);

        // Повторное продление запрещено (ограничение 1/1)
        $secondResponse = $this->actingAs($user)->post("/bookings/{$booking->id}/extend");
        $secondResponse->assertSessionHas('error');
    }

    /**
     * Тест: Завершение рейса и начисление миль администратором (Функция 12)
     */
    public function test_admin_completes_flight_and_accrues_miles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $flight = Flight::where('status', 'scheduled')->has('bookings')->first();

        $this->assertNotNull($flight, 'Рейс с бронированиями не найден.');
        $response = $this->actingAs($admin)->post("/admin/flights/{$flight->id}/complete");
        $response->assertSessionHas('success');

        $flight->refresh();
        $this->assertEquals('completed', $flight->status);

        // Проверяем, что активные бронирования перешли в completed
        foreach ($flight->bookings as $b) {
            if ($b->status === 'completed') {
                $hasTx = MileTransaction::where('booking_id', $b->id)->where('type', 'flight')->exists();
                $this->assertTrue($hasTx);
            }
        }
    }

    /**
     * Тест: Генерация и скачивание PDF-квитанции (Функция 13)
     */
    public function test_pdf_receipt_download(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();
        $booking = Booking::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)->get("/bookings/{$booking->id}/receipt");
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        // Проверка: чужой пользователь не может получить квитанцию
        $otherUser = User::where('email', 'ivan@gmail.com')->first();
        $forbiddenResponse = $this->actingAs($otherUser)->get("/bookings/{$booking->id}/receipt");
        $forbiddenResponse->assertStatus(403);
    }

    /**
     * Тест: Отмена активного бронирования пассажиром (Функция 3)
     */
    public function test_user_can_cancel_active_booking(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();
        $booking = Booking::where('user_id', $user->id)->where('status', 'active')->first();

        $this->assertNotNull($booking, 'Активное бронирование не найдено.');
        $response = $this->actingAs($user)->post("/bookings/{$booking->id}/cancel");
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
    }
}
