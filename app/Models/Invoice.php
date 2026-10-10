<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'reservation_id',
        'return_id',
        'subtotal',
        'fine_amount',
        'grand_total',
        'payment_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'fine_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function returnRecord(): BelongsTo
    {
        return $this->belongsTo(VehicleReturn::class, 'return_id');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedFineAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->fine_amount, 0, ',', '.');
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'unpaid' => 'bg-amber-100 text-amber-800 border-amber-200',
            'overdue' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
