<?php

namespace App\Modules\Teachers\Requests;

use App\Rules\FileInTempExcel;
use Illuminate\Foundation\Http\FormRequest;

class StoreExcelRequest extends FormRequest
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
            'file' => [
                'required',
                'string',
                new FileInTempExcel
            ]
        ];
    }
}
