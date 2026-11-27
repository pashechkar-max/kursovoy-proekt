<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flight_id',
        'seat_id',
        'booking_number',
        'booking_class',
        'price',
        'passenger_name',
        'passenger_surname',
        'passport_series',
        'passport_number',
        'status',
        'expires_at',
        'extension_count',
        'receipt_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'expires_at' => 'datetime',
            'extension_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function mileTransactions(): HasMany
    {
        return $this->hasMany(MileTransaction::class);
    }

    public function petBooking(): HasOne
    {
        return $this->hasOne(PetBooking::class);
    }

    public function hasPet(): bool
    {
        return $this->petBooking()->exists();
    }

    public function syncExpiredStatus(): void
    {
        if ($this->status === 'active' && $this->expires_at <= now()) {
            $this->status = 'expired';
            $this->save();
        }
    }

    public function isUpcoming(): bool
    {
        $this->syncExpiredStatus();
        return $this->status === 'active' && $this->flight && $this->flight->departure_time > now();
    }

    public function isPast(): bool
    {
        $this->syncExpiredStatus();
        return in_array($this->status, ['completed', 'expired', 'cancelled'])
            || ($this->flight && $this->flight->departure_time <= now());
    }

    public function canUpgrade(): bool
    {
        $this->syncExpiredStatus();
        return $this->status === 'active'
            && $this->expires_at > now()
            && $this->booking_class === 'economy';
    }

    public function canExtend(): bool
    {
        $this->syncExpiredStatus();
        return $this->status === 'active'
            && $this->expires_at > now()
            && $this->extension_count < 1;
    }
}
