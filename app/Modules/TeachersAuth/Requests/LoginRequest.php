<?php

namespace App\Modules\TeachersAuth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'user_name' => [
                'required',
                Rule::exists('users', 'user_name')->where(function ($query) {
                    $query->where('guard', ['teacher']);
                }),
            ],
            'password' => ['required', Password::min(6)],
        ];
    }
}
