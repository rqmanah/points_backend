<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class otpVerifyRequest extends FormRequest
{

    public function rules()
    {
        return [
            'otp' => 'required|string',
        ];
    }
}
