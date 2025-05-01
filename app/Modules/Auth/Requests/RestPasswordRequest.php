<?php

namespace App\Modules\Auth\Requests;

use App\Rules\PasswordComplexity;
use Illuminate\Foundation\Http\FormRequest;

class RestPasswordRequest extends FormRequest
{

    public function rules()
    {
        return [
            'old_password'     => 'required|string|min:6|max:255',
            'password'         =>  ['required', 'string', 'min:6' , 'confirmed' , new PasswordComplexity],
        ];
    }
}
