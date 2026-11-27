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

    public function test_guest_can_view_home_and_top_offers(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('AETHER AIRWAYS');
        $response->assertSee('ТОП-3 направления');
    }

    public function test_flight_search_and_filter(): void
    {
        $response = $this->get('/flights?from=Новосибирск&to=Сочи&price_sort=asc');
        $response->assertStatus(200);
        $response->assertSee('KS-501');
    }

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

        $transaction = MileTransaction::where('user_id', $user->id)->where('type', 'welcome')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(1000, $transaction->amount);
    }

    public function test_password_policy_enforcement(): void
    {

        $shortPassResponse = $this->post('/register', [
            'first_name' => 'Ольга',
            'last_name' => 'Иванова',
            'email' => 'olga_short@test.com',
            'password' => 'Pass1',
            'password_confirmation' => 'Pass1',
        ]);
        $shortPassResponse->assertSessionHasErrors(['password']);

        $onlyLettersResponse = $this->post('/register', [
            'first_name' => 'Ольга',
            'last_name' => 'Иванова',
            'email' => 'olga_letters@test.com',
            'password' => 'PasswordOnly',
            'password_confirmation' => 'PasswordOnly',
        ]);
        $onlyLettersResponse->assertSessionHasErrors(['password']);

        $onlyDigitsResponse = $this->post('/register', [
            'first_name' => 'Ольга',
            'last_name' => 'Иванова',
            'email' => 'olga_digits@test.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);
        $onlyDigitsResponse->assertSessionHasErrors(['password']);
    }

    public function test_unique_user_login_enforcement(): void
    {

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

    public function test_non_admin_redirect_to_home(): void
    {

        $guestResponse = $this->get('/admin');
        $guestResponse->assertRedirect('/login');

        $user = User::where('role', 'user')->first();
        $userResponse = $this->actingAs($user)->get('/admin');
        $userResponse->assertRedirect('/');
        $userResponse->assertSessionHas('error');

        $admin = User::where('role', 'admin')->first();
        $adminResponse = $this->actingAs($admin)->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Управление рейсами');
    }

    public function test_passport_validation_rules(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Александра',
            'surname' => 'Александрова',
            'passport_series' => 'ABC',
            'passport_number' => '12',
        ]);

        $response->assertSessionHasErrors(['passport_series', 'passport_number']);
    }

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

        $tx = MileTransaction::where('booking_id', $booking->id)->where('type', 'upgrade')->first();
        $this->assertNotNull($tx);
        $this->assertEquals(-2000, $tx->amount);
    }

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

        $secondResponse = $this->actingAs($user)->post("/bookings/{$booking->id}/extend");
        $secondResponse->assertSessionHas('error');
    }

    public function test_admin_completes_flight_and_accrues_miles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $flight = Flight::where('status', 'scheduled')->has('bookings')->first();

        $this->assertNotNull($flight, 'Рейс с бронированиями не найден.');
        $response = $this->actingAs($admin)->post("/admin/flights/{$flight->id}/complete");
        $response->assertSessionHas('success');

        $flight->refresh();
        $this->assertEquals('completed', $flight->status);

        foreach ($flight->bookings as $b) {
            if ($b->status === 'completed') {
                $hasTx = MileTransaction::where('booking_id', $b->id)->where('type', 'flight')->exists();
                $this->assertTrue($hasTx);
            }
        }
    }

    public function test_pdf_receipt_download(): void
    {
        $user = User::where('email', 'hello.alex@gmail.com')->first();
        $booking = Booking::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)->get("/bookings/{$booking->id}/receipt");
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        $otherUser = User::where('email', 'ivan@gmail.com')->first();
        $forbiddenResponse = $this->actingAs($otherUser)->get("/bookings/{$booking->id}/receipt");
        $forbiddenResponse->assertStatus(403);
    }

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

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Управление пользователями и ролями');
        $response->assertSee('Создать пользователя');
        $response->assertSee($admin->email);
    }

    public function test_admin_can_create_user_with_role_and_miles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $testEmail = 'newadmin_' . time() . '@test.com';

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Михаил',
            'surname' => 'Сергеев',
            'email' => $testEmail,
            'password' => 'SecurePass123',
            'role' => 'admin',
            'phone' => '+7 999 111-22-33',
            'miles' => 2500,
            'passport_series' => '4012',
            'passport_number' => '123456',
            'passport_issued_date' => '2020-05-15',
            'passport_issued_by' => 'МВД России',
            'birth_date' => '1995-10-10',
            'citizenship' => 'РФ',
        ]);

        $response->assertSessionHas('success');

        $created = User::where('email', $testEmail)->first();
        $this->assertNotNull($created);
        $this->assertEquals('admin', $created->role);
        $this->assertEquals(2500, $created->miles);
        $this->assertEquals('4012', $created->passport_series);
    }

    public function test_admin_can_update_user_and_toggle_role(): void
    {
        $admin = User::where('role', 'admin')->first();
        $targetUser = User::where('role', 'user')->first();

        $response = $this->actingAs($admin)->post("/admin/users/{$targetUser->id}", [
            'name' => 'ОбновленноеИмя',
            'surname' => $targetUser->surname,
            'email' => $targetUser->email,
            'role' => 'user',
            'phone' => '+7 900 777-66-55',
            'miles' => 3000,
        ]);

        $response->assertSessionHas('success');
        $targetUser->refresh();
        $this->assertEquals('ОбновленноеИмя', $targetUser->name);
        $this->assertEquals(3000, $targetUser->miles);

        $roleResponse = $this->actingAs($admin)->post("/admin/users/{$targetUser->id}/role");
        $roleResponse->assertSessionHas('success');
        $targetUser->refresh();
        $this->assertEquals('admin', $targetUser->role);
    }

    public function test_admin_cannot_demote_or_delete_self(): void
    {
        $admin = User::where('role', 'admin')->first();

        $demoteResponse = $this->actingAs($admin)->post("/admin/users/{$admin->id}/role");
        $demoteResponse->assertSessionHas('error');

        $deleteResponse = $this->actingAs($admin)->post("/admin/users/{$admin->id}/delete");
        $deleteResponse->assertSessionHas('error');

        $admin->refresh();
        $this->assertEquals('admin', $admin->role);
        $this->assertNotNull(User::find($admin->id));
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::where('role', 'admin')->first();
        $userToDelete = User::create([
            'name' => 'Удаляемый',
            'surname' => 'Пользователь',
            'email' => 'to_delete_' . time() . '@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'user',
            'miles' => 500,
        ]);

        $response = $this->actingAs($admin)->post("/admin/users/{$userToDelete->id}/delete");
        $response->assertSessionHas('success');

        $this->assertNull(User::find($userToDelete->id));
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $regularUser = User::where('role', 'user')->first();
        $targetUser = User::where('role', 'admin')->first();

        $response = $this->actingAs($regularUser)->get('/admin');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');

        $createResponse = $this->actingAs($regularUser)->post('/admin/users', [
            'name' => 'Хакер',
            'surname' => 'Взломщик',
            'email' => 'hack@test.com',
            'password' => 'Password123',
            'role' => 'admin',
        ]);
        $createResponse->assertRedirect('/');
        $createResponse->assertSessionHas('error');

        $roleResponse = $this->actingAs($regularUser)->post("/admin/users/{$targetUser->id}/role");
        $roleResponse->assertRedirect('/');
        $roleResponse->assertSessionHas('error');

        $deleteResponse = $this->actingAs($regularUser)->post("/admin/users/{$targetUser->id}/delete");
        $deleteResponse->assertRedirect('/');
        $deleteResponse->assertSessionHas('error');
    }
}
