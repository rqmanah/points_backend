<?php

namespace App\Modules\StudentAuth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'national_id' => [
                'required',
                Rule::exists('users', 'national_id')->where(function ($query) {
                    $query->where('guard', ['student']);
                }),
            ],

            'password' => ['required', Password::min(6)],
        ];
    }
}
