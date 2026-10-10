<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAdminPrivileges();
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle') ? $this->route('vehicle')->id : $this->route('id');

        return [
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehicles', 'license_plate')->ignore($vehicleId),
            ],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'transmission' => ['required', 'in:manual,automatic'],
            'rental_rate_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,rented,maintenance,out_of_service'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
