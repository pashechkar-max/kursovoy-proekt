<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function show(Request $request)
    {
        $user = Auth::user();

        Booking::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $allBookings = $user->bookings()
            ->with(['flight', 'seat'])
            ->orderByDesc('created_at')
            ->get();

        $upcomingBookings = $allBookings->filter(fn ($b) => $b->isUpcoming());
        $pastBookings = $allBookings->filter(fn ($b) => $b->isPast());

        $transactions = $user->mileTransactions()
            ->orderByDesc('created_at')
            ->get();

        $activeTab = $request->query('tab', 'tabBookings');

        return view('profile', compact(
            'user',
            'allBookings',
            'upcomingBookings',
            'pastBookings',
            'transactions',
            'activeTab'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'surname' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^(\+7|7|8)?[\s\-]?\(?[0-9]{3}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
            ],
            'birth_date' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01'
            ],
            'passport_series' => [
                'nullable',
                'string',
                'regex:/^[0-9]{4}$/'
            ],
            'passport_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{6}$/'
            ],
            'passport_issued_date' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],
            'passport_issued_by' => [
                'nullable',
                'string',
                'min:3',
                'max:255'
            ],
            'citizenship' => [
                'nullable',
                'string',
                'max:100'
            ],
        ], [
            'name.required' => 'Пожалуйста, укажите имя пассажира.',
            'name.min' => 'Имя должно содержать не менее 2 символов.',
            'name.regex' => 'Имя может содержать только буквы.',
            'surname.required' => 'Пожалуйста, укажите фамилию пассажира.',
            'surname.min' => 'Фамилия должна содержать не менее 2 символов.',
            'surname.regex' => 'Фамилия может содержать только буквы.',
            'phone.regex' => 'Введите корректный номер телефона (например: +7 (999) 000-00-00).',
            'birth_date.before' => 'Дата рождения не может быть в будущем.',
            'birth_date.after' => 'Указана некорректная дата рождения.',
            'passport_series.regex' => 'Серия паспорта гражданина РФ должна состоять ровно из 4 цифр.',
            'passport_number.regex' => 'Номер паспорта гражданина РФ должен состоять ровно из 6 цифр.',
            'passport_issued_date.before_or_equal' => 'Дата выдачи паспорта не может быть в будущем.',
            'passport_issued_by.min' => 'Поле «Кем выдан паспорт» должно содержать не менее 3 символов.',
        ]);

        if (!empty($validated['passport_series']) && !empty($validated['passport_number'])) {
            $existing = User::where('passport_series', $validated['passport_series'])
                ->where('passport_number', $validated['passport_number'])
                ->where('id', '!=', $user->id)
                ->first();

            if ($existing) {
                return back()->withErrors([
                    'passport_number' => 'Пассажир с указанной серией и номером паспорта уже зарегистрирован в системе.',
                ])->withInput();
            }
        }

        $user->update($validated);

        return redirect()->route('profile', ['tab' => 'tabPassport'])
            ->with('success', 'Персональные и паспортные данные успешно сохранены!');
    }
}
