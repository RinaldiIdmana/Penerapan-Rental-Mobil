<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    /**
     * Check if a vehicle is available for a specified date range.
     * Prevents overlapping with reservations having status 'confirmed' or 'active'.
     */
    public function isVehicleAvailable(int $vehicleId, string $startDate, string $endDate, ?int $excludeReservationId = null): bool
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle || in_array($vehicle->status, ['maintenance', 'out_of_service'], true)) {
            return false;
        }

        $overlapExists = Reservation::query()
            ->where('vehicle_id', $vehicleId)
            ->activeOrConfirmed()
            ->overlapping($startDate, $endDate, $excludeReservationId)
            ->exists();

        return !$overlapExists;
    }

    /**
     * Calculate rental days and total cost based on start and end dates.
     */
    public function calculateDaysAndAmount(Vehicle $vehicle, string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lessThan($start)) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal selesai sewa tidak boleh sebelum tanggal mulai.',
            ]);
        }

        // Rental days: if same day, count as 1 day; otherwise difference in days + 1
        $diff = $start->diffInDays($end);
        $totalDays = max(1, (int) $diff);

        $baseAmount = $totalDays * (float) $vehicle->rental_rate_per_day;

        return [
            'total_days' => $totalDays,
            'base_amount' => $baseAmount,
        ];
    }

    /**
     * Generate unique reservation code: RSV-YYYYMMDD-XXXX
     */
    public function generateReservationCode(): string
    {
        $datePrefix = Carbon::now()->format('Ymd');
        $randomSuffix = strtoupper(substr(uniqid(), -4));
        $code = "RSV-{$datePrefix}-{$randomSuffix}";

        while (Reservation::where('reservation_code', $code)->exists()) {
            $randomSuffix = strtoupper(substr(uniqid(), -4));
            $code = "RSV-{$datePrefix}-{$randomSuffix}";
        }

        return $code;
    }

    /**
     * Generate unique invoice number: INV/YYYYMMDD/XXXX
     */
    public function generateInvoiceNumber(): string
    {
        $datePart = Carbon::now()->format('Ymd');
        $prefix = "INV/{$datePart}/";

        $latestInvoice = Invoice::where('invoice_number', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($latestInvoice) {
            $lastSequence = (int) substr($latestInvoice->invoice_number, -4);
            $newSequence = str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }

        return "{$prefix}{$newSequence}";
    }

    /**
     * Create a new reservation along with anti-overlap validation and automatic initial invoice.
     */
    public function createReservation(array $data, User $user): Reservation
    {
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        if (!$this->isVehicleAvailable($vehicle->id, $data['start_date'], $data['end_date'])) {
            throw ValidationException::withMessages([
                'vehicle_id' => 'Kendaraan ini sudah dipesan pada rentang tanggal tersebut. Silakan pilih armada atau jadwal lain.',
            ]);
        }

        $calculation = $this->calculateDaysAndAmount($vehicle, $data['start_date'], $data['end_date']);

        return DB::transaction(function () use ($data, $user, $vehicle, $calculation) {
            $reservation = Reservation::create([
                'reservation_code' => $this->generateReservationCode(),
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_days' => $calculation['total_days'],
                'base_amount' => $calculation['base_amount'],
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Auto-generate invoice
            Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'reservation_id' => $reservation->id,
                'return_id' => null,
                'subtotal' => $calculation['base_amount'],
                'fine_amount' => 0,
                'grand_total' => $calculation['base_amount'],
                'payment_status' => 'unpaid',
            ]);

            // If immediately confirmed and starts today, set vehicle to rented
            if ($reservation->status === 'confirmed' && Carbon::parse($reservation->start_date)->isToday()) {
                $vehicle->update(['status' => 'rented']);
            }

            return $reservation->load(['vehicle', 'user', 'invoice']);
        });
    }

    /**
     * Confirm a reservation.
     */
    public function confirmReservation(Reservation $reservation): Reservation
    {
        if (!$this->isVehicleAvailable($reservation->vehicle_id, $reservation->start_date->toDateString(), $reservation->end_date->toDateString(), $reservation->id)) {
            throw ValidationException::withMessages([
                'status' => 'Tidak dapat mengonfirmasi. Terdapat jadwal bentrok dengan reservasi lain.',
            ]);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'confirmed']);

            if (Carbon::parse($reservation->start_date)->isToday() || Carbon::parse($reservation->start_date)->isPast()) {
                $reservation->vehicle->update(['status' => 'rented']);
                $reservation->update(['status' => 'active']);
            }
        });

        return $reservation->fresh();
    }

    /**
     * Cancel a reservation.
     */
    public function cancelReservation(Reservation $reservation): Reservation
    {
        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);
            
            // Release vehicle if it was marked as rented by this reservation
            if ($reservation->vehicle->status === 'rented') {
                $reservation->vehicle->update(['status' => 'available']);
            }
        });

        return $reservation->fresh();
    }
}
