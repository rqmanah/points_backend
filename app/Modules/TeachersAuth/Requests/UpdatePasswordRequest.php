<?php

namespace App\Modules\TeachersAuth\Requests;

use App\Rules\PasswordComplexity;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{

    public function rules()
    {
        return [
            'old_password' => 'required|min:6',
            'password'     =>  ['required', 'confirmed', 'min:6', new PasswordComplexity, 'max:255'],
        ];
    }
}
