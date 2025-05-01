<?php

namespace App\Modules\Auth\Requests;

use App\Rules\PasswordComplexity;
use Illuminate\Foundation\Http\FormRequest;

class resetPasswordRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email' => 'required|email',
            'otp' => 'required',
            'password' =>['required', 'string', 'min:6', 'max:255', new PasswordComplexity],
        ];
    }
}
