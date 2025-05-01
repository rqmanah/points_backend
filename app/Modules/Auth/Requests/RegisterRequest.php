<?php

namespace App\Modules\Auth\Requests;

use App\Rules\PasswordComplexity;
use App\Rules\UniquePhoneStore;
use App\Rules\UserNameCheck;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'dialing_code' => 'required|string|max:5',
            'phone'        => [
                'required',
                'digits_between:5,15',
                'max:15',
                new UniquePhoneStore,
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            'national_id' => 'nullable|numeric|digits_between:5,15|unique:users,national_id',
            'gender'   => 'required|in:male,female',
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed', new PasswordComplexity],
        ];
    }
}
