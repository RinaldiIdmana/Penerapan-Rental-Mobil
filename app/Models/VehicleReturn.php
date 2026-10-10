<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VehicleReturn extends Model
{
    use HasFactory;

    /**
     * Map model to 'returns' table as requested in specification.
     */
    protected $table = 'returns';

    protected $fillable = [
        'reservation_id',
        'return_date',
        'late_days',
        'fine_per_day',
        'total_fine',
        'condition_notes',
        'final_total_amount',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'late_days' => 'integer',
            'fine_per_day' => 'decimal:2',
            'total_fine' => 'decimal:2',
            'final_total_amount' => 'decimal:2',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'return_id');
    }

    public function getFormattedTotalFineAttribute(): string
    {
        return 'Rp ' . number_format($this->total_fine, 0, ',', '.');
    }

    public function getFormattedFinalTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->final_total_amount, 0, ',', '.');
    }
}
