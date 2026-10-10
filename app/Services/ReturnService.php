<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\VehicleReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    /**
     * Preview return calculations before submitting.
     */
    public function previewReturn(Reservation $reservation, string $returnDate, ?float $customFineRate = null): array
    {
        $scheduledEnd = Carbon::parse($reservation->end_date)->startOfDay();
        $actualReturn = Carbon::parse($returnDate)->startOfDay();

        $lateDays = 0;
        if ($actualReturn->greaterThan($scheduledEnd)) {
            $lateDays = (int) $scheduledEnd->diffInDays($actualReturn);
        }

        // Default fine per day is 50% of the vehicle daily rate, or custom rate if specified
        $fineRatePerDay = $customFineRate ?? max(50000, round((float) $reservation->vehicle->rental_rate_per_day * 0.5, 2));
        $totalFine = $lateDays * $fineRatePerDay;
        $finalTotalAmount = (float) $reservation->base_amount + $totalFine;

        return [
            'scheduled_end_date' => $reservation->end_date->toDateString(),
            'actual_return_date' => $actualReturn->toDateString(),
            'late_days' => $lateDays,
            'fine_per_day' => $fineRatePerDay,
            'total_fine' => $totalFine,
            'base_amount' => (float) $reservation->base_amount,
            'final_total_amount' => $finalTotalAmount,
        ];
    }

    /**
     * Process vehicle return and update reservation, vehicle status, and invoice.
     */
    public function processReturn(Reservation $reservation, array $data): VehicleReturn
    {
        if (in_array($reservation->status, ['completed', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'reservation_id' => 'Reservasi ini sudah selesai atau dibatalkan.',
            ]);
        }

        $preview = $this->previewReturn(
            $reservation,
            $data['return_date'],
            isset($data['fine_per_day']) ? (float) $data['fine_per_day'] : null
        );

        return DB::transaction(function () use ($reservation, $data, $preview) {
            // 1. Create Return record in 'returns' table
            $vehicleReturn = VehicleReturn::create([
                'reservation_id' => $reservation->id,
                'return_date' => $data['return_date'],
                'late_days' => $preview['late_days'],
                'fine_per_day' => $preview['fine_per_day'],
                'total_fine' => $preview['total_fine'],
                'condition_notes' => $data['condition_notes'] ?? null,
                'final_total_amount' => $preview['final_total_amount'],
            ]);

            // 2. Update Reservation status
            $reservation->update([
                'status' => 'completed',
            ]);

            // 3. Update Vehicle status
            $needsMaintenance = !empty($data['needs_maintenance']);
            $newVehicleStatus = $needsMaintenance ? 'maintenance' : 'available';
            $reservation->vehicle->update([
                'status' => $newVehicleStatus,
            ]);

            // 4. Update or link Invoice
            $invoice = $reservation->invoice;
            if ($invoice) {
                $hasPaymentMade = $invoice->payment_status === 'paid' && $preview['total_fine'] == 0;

                $invoice->update([
                    'return_id' => $vehicleReturn->id,
                    'fine_amount' => $preview['total_fine'],
                    'grand_total' => $preview['final_total_amount'],
                    // If fine added to previously paid invoice, change status back to unpaid
                    'payment_status' => $hasPaymentMade ? 'paid' : ($invoice->payment_status === 'paid' && $preview['total_fine'] > 0 ? 'unpaid' : $invoice->payment_status),
                ]);
            }

            return $vehicleReturn->load(['reservation.vehicle', 'reservation.user', 'invoice']);
        });
    }
}
