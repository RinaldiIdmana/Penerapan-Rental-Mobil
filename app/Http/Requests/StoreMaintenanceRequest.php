<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAdminPrivileges();
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'service_date' => ['required', 'date'],
            'service_type' => ['required', 'string', 'max:150'],
            'cost' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'next_service_due_date' => ['required', 'date', 'after_or_equal:service_date'],
            'status' => ['required', 'in:scheduled,in_progress,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'next_service_due_date.after_or_equal' => 'Tanggal jatuh tempo servis berikutnya harus sama atau setelah tanggal servis.',
            'vehicle_id.required' => 'Pilih armada yang akan diservis.',
        ];
    }
}
