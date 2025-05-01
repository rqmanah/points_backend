<?php

namespace App\Modules\FileService\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            'file' => 'required|mimes:jpeg,png,jpg,xls,xlsx,webp,pdf,doc,docx,txt|max:2048',
        ];
    }

}
