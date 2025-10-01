<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\InputSanitizerTrait;
use Illuminate\Foundation\Http\FormRequest;

class CategoryFinanceRequest extends FormRequest
{
    use InputSanitizerTrait;
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
            'uuid' => 'nullable|string|max:36',
            'name_category_finances' => 'required|string|max:255|min:2',
        ];
    }

}
