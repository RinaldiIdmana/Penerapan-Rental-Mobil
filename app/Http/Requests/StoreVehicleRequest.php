<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAdminPrivileges();
    }

    public function rules(): array
    {
        return [
            'license_plate' => ['required', 'string', 'max:20', 'unique:vehicles,license_plate'],
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

    public function messages(): array
    {
        return [
            'license_plate.unique' => 'Plat nomor kendaraan sudah terdaftar di sistem.',
            'license_plate.required' => 'Plat nomor wajib diisi.',
            'rental_rate_per_day.required' => 'Tarif sewa per hari wajib ditentukan.',
        ];
    }
}
