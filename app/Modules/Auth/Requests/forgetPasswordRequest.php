<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class forgetPasswordRequest extends FormRequest
{

    public function rules()
    {
        return [

           'email' => 'required|email',
        ];
    }
}
