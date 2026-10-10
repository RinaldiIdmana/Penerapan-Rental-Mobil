<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_date',
        'service_type',
        'cost',
        'description',
        'next_service_due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'next_service_due_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope for services approaching due date (within $days days) or overdue.
     */
    public function scopeDueSoonOrOverdue(Builder $query, int $days = 7): Builder
    {
        $threshold = Carbon::now()->addDays($days)->toDateString();

        return $query->whereIn('status', ['scheduled', 'in_progress'])
            ->where('next_service_due_date', '<=', $threshold);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereIn('status', ['scheduled', 'in_progress'])
            ->where('next_service_due_date', '<', Carbon::now()->toDateString());
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed' &&
            $this->next_service_due_date &&
            Carbon::parse($this->next_service_due_date)->isPast();
    }

    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->next_service_due_date) {
            return 999;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays(Carbon::parse($this->next_service_due_date)->startOfDay(), false);
    }

    public function getFormattedCostAttribute(): string
    {
        return 'Rp ' . number_format($this->cost, 0, ',', '.');
    }
}
