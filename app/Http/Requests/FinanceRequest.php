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
            'users_id' => 'integer',
            'category_finances_id' => 'required|integer',
            'name_item' => 'required|string|max:255',
            'price' => 'required|string',
            'purchase_date' => 'required|date',
            'purchase_by' => 'required|string|max:255',
        ];
    }
}
