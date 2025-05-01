<?php

namespace App\Modules\Teachers\Requests;

use App\Rules\PasswordComplexity;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'teacher_id' => 'required|integer|exists:users,id',
            'password'     => ['required', 'string', 'min:6', 'confirmed', new PasswordComplexity],
        ];
    }
}
