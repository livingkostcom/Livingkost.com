<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-invoice');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'month_year' => 'required|date_format:Y-m|unique:invoices,month_year,NULL,id,lease_id,' . $this->lease_id,
            'due_date' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'lease_id.required' => 'Lease harus dipilih',
            'lease_id.exists' => 'Lease tidak ditemukan',
            'amount.required' => 'Jumlah tagihan harus diisi',
            'amount.numeric' => 'Jumlah tagihan harus berupa angka',
            'month_year.unique' => 'Tagihan untuk bulan ini sudah ada',
            'due_date.required' => 'Tanggal jatuh tempo harus diisi',
        ];
    }
}
