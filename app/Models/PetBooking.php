<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'transport_type',
        'pet_type',
        'pet_name',
        'breed',
        'weight',
        'carrier_dimensions',
        'vet_passport_number',
        'rabies_vaccinated',
        'service_fee',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'rabies_vaccinated' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transportTypeName(): string
    {
        return match ($this->transport_type) {
            'cabin' => 'В салоне под креслом (PETC)',
            'seat' => 'На отдельном месте (EUPS)',
            'hold' => 'В багажном отсеке (AVIH)',
            'guide' => 'Собака-поводырь (SVAN)',
            default => 'Перевозка питомца',
        };
    }

    public function petTypeName(): string
    {
        return match ($this->pet_type) {
            'dog' => 'Собака',
            'cat' => 'Кошка',
            'bird' => 'Птица',
            default => 'Питомец',
        };
    }
}
