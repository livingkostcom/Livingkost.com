<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit-room');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:active,closed,terminated',
            'due_date_per_month' => 'sometimes|integer|between:1,28',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'end_date.date' => 'Tanggal akhir harus berupa tanggal yang valid',
            'end_date.after' => 'Tanggal akhir harus setelah tanggal mulai',
            'status.in' => 'Status tidak valid',
            'due_date_per_month.between' => 'Tanggal jatuh tempo harus antara 1-28',
        ];
    }
}
