<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryFinanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->input('uuid');
        return [
            'uuid' => [
                'max:36',
            ],
            'name_category_finances' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_category_finances' => 'category name',
        ];
    }
}
