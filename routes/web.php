<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

Route::middleware(['throttle:global'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/animals', [HomeController::class, 'animals'])->name('animals');
    Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');
    Route::get('/flights/{flight}/seats', [FlightController::class, 'getSeats'])->name('flights.seats');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('throttle:bookings');
        Route::post('/bookings/{booking}/upgrade', [BookingController::class, 'upgrade'])->name('bookings.upgrade');
        Route::post('/bookings/{booking}/extend', [BookingController::class, 'extend'])->name('bookings.extend');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/bookings/{booking}/receipt', [BookingController::class, 'downloadReceipt'])->name('bookings.receipt');
        Route::get('/bookings/{booking}/receipt/view', [BookingController::class, 'viewReceipt'])->name('bookings.receipt.view');
    });

    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/flights/{flight}/complete', [AdminController::class, 'completeFlight'])->name('flights.complete');
        Route::post('/flights/{flight}/status', [AdminController::class, 'updateFlightStatus'])->name('flights.status');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{user}/role', [AdminController::class, 'toggleUserRole'])->name('users.role');
        Route::post('/users/{user}/delete', [AdminController::class, 'destroyUser'])->name('users.destroy');
    });
});
