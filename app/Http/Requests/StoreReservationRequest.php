<?php

namespace App\Http\Requests;

use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'customer_id' => ['nullable', 'exists:users,id'], // If admin books on behalf of customer
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled(['vehicle_id', 'start_date', 'end_date'])) {
                $reservationService = app(ReservationService::class);
                $isAvailable = $reservationService->isVehicleAvailable(
                    (int) $this->vehicle_id,
                    $this->start_date,
                    $this->end_date
                );

                if (!$isAvailable) {
                    $validator->errors()->add(
                        'vehicle_id',
                        'Kendaraan ini sudah memiliki jadwal reservasi aktif/terkonfirmasi pada rentang tanggal yang dipilih.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Tanggal mulai sewa minimal hari ini.',
            'end_date.after_or_equal' => 'Tanggal selesai sewa harus sama atau setelah tanggal mulai.',
            'vehicle_id.exists' => 'Kendaraan yang dipilih tidak valid.',
        ];
    }
}
