<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nama_tagihan' => 'string|max:255',
            'harga_tagihan' => 'string|max:255',
            'tanggal_tagihan' => 'string|max:255',
            'pemilik_tagihan' => 'string|max:255',
            'siklus_tagihan' => 'string|max:255', // 1 bulanan, 2 tahunan
            'jatuh_tempo_tagihan' => 'string|max:255',
            'metode_pembayaran' => 'string|max:255',
            'keterangan_tagihan' => 'string|max:255',
        ];
    }
}
