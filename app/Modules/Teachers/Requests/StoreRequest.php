<?php

namespace App\Modules\Teachers\Requests;

use App\Rules\UserNameCheck;
use App\Rules\PasswordComplexity;
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
            'name'         => 'required|string|max:255|min:3',
            'user_name'    => ['nullable', 'string', 'max:255', 'min:3', new UserNameCheck, 'unique:users,user_name'],
            'national_id'  => 'nullable|unique:users,national_id|digits_between:5,15',
            'password'     => ['required','string','min:6','max:255' , new PasswordComplexity],
            'is_active'    => 'required|boolean'
        ];
    }
}
