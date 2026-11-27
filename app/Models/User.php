<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'birth_date',
        'passport_series',
        'passport_number',
        'passport_issued_date',
        'passport_issued_by',
        'citizenship',
        'miles',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'birth_date' => 'date',
            'passport_issued_date' => 'date',
            'miles' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function mileTransactions(): HasMany
    {
        return $this->hasMany(MileTransaction::class);
    }

    public function petBookings(): HasMany
    {
        return $this->hasMany(PetBooking::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasCompletePassport(): bool
    {
        return !empty($this->name)
            && !empty($this->surname)
            && !empty($this->passport_series)
            && !empty($this->passport_number)
            && !empty($this->passport_issued_date)
            && !empty($this->passport_issued_by);
    }
}
