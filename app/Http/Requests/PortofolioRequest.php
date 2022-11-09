<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class PortofolioRequest extends FormRequest
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
            'thumbnail' => ['required', File::image()->max(2 * 1024), 'mimes:jpeg,jpg,png', 'mimetypes:image/jpeg,image/jpg,image/png'],
            'title' => ['required', 'string'],
            'url' => ['required', 'string'],
            'kategori' => ['required', 'string'],
            'published' => ['required', 'string']
        ];
    }
}
