<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceRequest extends FormRequest
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
            'users_uuid' => 'required|string|uuid',
            'category_finances_uuid' => 'required|string|uuid',
            'name_item' => 'required|string|max:255',
            'price' => 'required|string',
            'payment_methods_uuid' => 'required|string|uuid',
            'purchase_by' => 'required|string|max:255',
        ];
    }
}
