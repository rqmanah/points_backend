<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'user_name' => [
                // required if phone is empty
                'required',
                Rule::exists('users', 'user_name')->where(function ($query) {
                    $query->whereIn('guard', ['manager' , 'student' , 'teacher']);
                }),
            ],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
