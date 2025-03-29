<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryIncomeRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'id' => ['nullable', 'integer'],
            'name_category_incomes' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_incomes', 'name_category_incomes')->ignore($id),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_category_incomes' => 'category name',
        ];
    }
}
