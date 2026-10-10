<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAdminPrivileges();
    }

    public function rules(): array
    {
        return [
            'return_date' => ['required', 'date'],
            'fine_per_day' => ['nullable', 'numeric', 'min:0'],
            'condition_notes' => ['nullable', 'string', 'max:1000'],
            'needs_maintenance' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_date.required' => 'Tanggal pengembalian armada wajib diisi.',
            'fine_per_day.numeric' => 'Besaran tarif denda per hari harus berupa angka yang valid.',
        ];
    }
}
