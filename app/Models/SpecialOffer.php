<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SpecialOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'title',
        'description',
        'image',
        'discount',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'decimal:2',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Scope для активных предложений с действующим сроком
     */
    public function scopeActive(Builder $query): Builder
    {
        $today = now()->toDateString();
        return $query->where('is_active', true)
                     ->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }
}
