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

    /**
     * Автоматическая проверка и обновление статуса expired, если срок бронирования истёк
     */
    public function syncExpiredStatus(): void
    {
        if ($this->status === 'active' && $this->expires_at <= now()) {
            $this->status = 'expired';
            $this->save();
        }
    }

    /**
     * Предстоящее бронирование: статус active и вылет в будущем
     */
    public function isUpcoming(): bool
    {
        $this->syncExpiredStatus();
        return $this->status === 'active' && $this->flight && $this->flight->departure_time > now();
    }

    /**
     * Прошедшее бронирование: completed, expired, cancelled или рейс уже вылетел
     */
    public function isPast(): bool
    {
        $this->syncExpiredStatus();
        return in_array($this->status, ['completed', 'expired', 'cancelled'])
            || ($this->flight && $this->flight->departure_time <= now());
    }

    /**
     * Доступен ли апгрейд (только active, не истекло, класс economy)
     */
    public function canUpgrade(): bool
    {
        $this->syncExpiredStatus();
        return $this->status === 'active'
            && $this->expires_at > now()
            && $this->booking_class === 'economy';
    }

    /**
     * Доступно ли продление (только active, не истекло, extension_count == 0)
     */
    public function canExtend(): bool
    {
        $this->syncExpiredStatus();
        return $this->status === 'active'
            && $this->expires_at > now()
            && $this->extension_count < 1;
    }
}
