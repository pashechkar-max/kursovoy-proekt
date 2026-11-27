<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'departure_city',
        'arrival_city',
        'departure_time',
        'arrival_time',
        'price',
        'status',
        'pet_cabin_quota',
        'pet_hold_quota',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'arrival_time' => 'datetime',
            'price' => 'decimal:2',
            'pet_cabin_quota' => 'integer',
            'pet_hold_quota' => 'integer',
        ];
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function specialOffers(): HasMany
    {
        return $this->hasMany(SpecialOffer::class);
    }

    public function activePetCabinCount(): int
    {
        return PetBooking::whereHas('booking', function ($q) {
            $q->where('flight_id', $this->id)
              ->where('status', 'active')
              ->where('expires_at', '>', now());
        })->whereIn('transport_type', ['cabin', 'seat'])->count();
    }

    public function availablePetCabinQuota(): int
    {
        $limit = $this->pet_cabin_quota ?? 4;
        return max(0, $limit - $this->activePetCabinCount());
    }

    public function activePetHoldCount(): int
    {
        return PetBooking::whereHas('booking', function ($q) {
            $q->where('flight_id', $this->id)
              ->where('status', 'active')
              ->where('expires_at', '>', now());
        })->where('transport_type', 'hold')->count();
    }

    public function availablePetHoldQuota(): int
    {
        $limit = $this->pet_hold_quota ?? 3;
        return max(0, $limit - $this->activePetHoldCount());
    }

    public function hasAvailablePetSlots(): bool
    {
        return $this->availablePetCabinQuota() > 0 || $this->availablePetHoldQuota() > 0;
    }

    public function scopeAvailableForBooking(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
                     ->where('departure_time', '>', now());
    }
}
