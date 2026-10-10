<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'brand',
        'model',
        'year',
        'transmission',
        'rental_rate_per_day',
        'status',
        'image_url',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'rental_rate_per_day' => 'decimal:2',
            'capacity' => 'integer',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function activeReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->whereIn('status', ['confirmed', 'active']);
    }

    public function latestMaintenance(): HasOne
    {
        return $this->hasOne(Maintenance::class)->latestOfMany('service_date');
    }

    /**
     * Scope for vehicles available for rent (not currently in maintenance or out of service).
     */
    public function scopeAvailableForRent(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->year})";
    }

    public function getFormattedRateAttribute(): string
    {
        return 'Rp ' . number_format($this->rental_rate_per_day, 0, ',', '.');
    }
}
