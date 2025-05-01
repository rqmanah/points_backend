<?php

namespace App\Modules\Prizes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'price' => [
                'required',
                'numeric',
                'min:1',
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:1',
            ],
            'image' => ['required', 'string'],
            "min_stock" => [
                'required',
                'numeric',
                'min:0',
            ],
            'order' => ['required', 'numeric', 'min:0'],
        ];
    }
}
