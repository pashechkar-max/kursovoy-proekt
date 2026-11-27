<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MileTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('profile');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'Пожалуйста, введите ваш логин (адрес электронной почты).',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'password.required' => 'Пожалуйста, введите пароль.',
            'password.min' => 'Пароль не может быть короче 6 символов.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('profile'))
                ->with('success', 'С возвращением в Aether Airways!');
        }

        return back()->withErrors([
            'email' => 'Неверный логин или пароль. Пожалуйста, проверьте введённые данные.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('profile');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Zа-яА-ЯёЁ])(?=.*\d).+$/u',
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string'
            ],
        ], [
            'first_name.required' => 'Пожалуйста, укажите ваше имя.',
            'first_name.min' => 'Имя должно содержать не менее 2 символов.',
            'first_name.regex' => 'Имя может содержать только буквы и дефис.',
            'last_name.required' => 'Пожалуйста, укажите вашу фамилию.',
            'last_name.min' => 'Фамилия должна содержать не менее 2 символов.',
            'last_name.regex' => 'Фамилия может содержать только буквы и дефис.',
            'email.required' => 'Пожалуйста, введите ваш email (логин).',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Пользователь с таким логином (email) уже зарегистрирован в системе. Выберите другой email или выполните вход.',
            'password.required' => 'Пожалуйста, задайте пароль.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.regex' => 'Пароль обязательно должен содержать как буквы, так и цифры.',
            'password.confirmed' => 'Введённые пароли не совпадают.',
            'password_confirmation.required' => 'Пожалуйста, подтвердите пароль.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['first_name'],
                'surname' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'miles' => 1000,
                'role' => 'user',
            ]);

            MileTransaction::create([
                'user_id' => $user->id,
                'booking_id' => null,
                'type' => 'welcome',
                'amount' => 1000,
                'description' => 'Приветственные мили за регистрацию (Welcome Miles)',
            ]);

            Auth::login($user);
            $request->session()->regenerate();
        });

        return redirect()->route('profile')
            ->with('success', 'Добро пожаловать в Aether Airways! Ваш аккаунт успешно создан, вам начислено 1 000 приветственных миль.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('info', 'Вы успешно вышли из системы.');
    }
}
