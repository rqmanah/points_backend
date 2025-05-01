<?php

namespace App\Modules\Students\Requests;

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
            'student_id' => 'required|integer|exists:users,id',
            'password'     => ['required', 'string', 'min:6', 'max:255', 'confirmed', new PasswordComplexity],
        ];
    }
}
