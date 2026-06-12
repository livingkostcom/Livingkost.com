<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('verify-payment');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:unpaid,pending,paid',
            'proof_of_payment' => 'sometimes|required_if:status,pending|file|mimes:jpg,jpeg,png,pdf|max:5120|dimensions:min_width=800,min_height=600',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid',
            'proof_of_payment.file' => 'Bukti pembayaran harus berupa file',
            'proof_of_payment.mimes' => 'Format file harus jpg, png, atau pdf',
            'proof_of_payment.max' => 'Ukuran file maksimal 5MB',
        ];
    }
}
