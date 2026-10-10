<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_code',
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'total_days',
        'base_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'base_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(VehicleReturn::class, 'reservation_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'reservation_id');
    }

    /**
     * Scope query to find reservations that overlap with a specific date range.
     * Formula: start_date <= $requestedEnd AND end_date >= $requestedStart
     */
    public function scopeOverlapping(Builder $query, string $startDate, string $endDate, ?int $excludeReservationId = null): Builder
    {
        $query->where(function ($q) use ($startDate, $endDate) {
            $q->where('start_date', '<=', $endDate)
              ->where('end_date', '>=', $startDate);
        });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query;
    }

    /**
     * Scope query for reservations that block availability ('confirmed' or 'active').
     */
    public function scopeActiveOrConfirmed(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmed', 'active']);
    }

    public function getFormattedBaseAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->base_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
            'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
