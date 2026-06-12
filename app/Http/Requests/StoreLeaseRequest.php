<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-room');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'room_id' => 'required|exists:rooms,id|unique:leases,room_id,NULL,id,status,active',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'due_date_per_month' => 'required|integer|between:1,28',
            'deposit_amount' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'tenant_id.required' => 'Penyewa harus dipilih',
            'tenant_id.exists' => 'Penyewa tidak ditemukan',
            'room_id.required' => 'Kamar harus dipilih',
            'room_id.exists' => 'Kamar tidak ditemukan',
            'room_id.unique' => 'Kamar sudah dihuni',
            'start_date.required' => 'Tanggal mulai harus diisi',
            'end_date.required' => 'Tanggal akhir harus diisi',
            'end_date.after' => 'Tanggal akhir harus setelah tanggal mulai',
            'due_date_per_month.required' => 'Tanggal jatuh tempo per bulan harus diisi',
            'deposit_amount.required' => 'Jumlah deposit harus diisi',
        ];
    }
}
