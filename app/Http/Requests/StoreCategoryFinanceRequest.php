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
        $id = $this->input('id');
        return [
            'id' => ['nullable', 'integer'],
            'name_category_finances' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_finances', 'name_category_finances')->ignore($id),
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
