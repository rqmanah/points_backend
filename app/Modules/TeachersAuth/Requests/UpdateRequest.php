<?php

namespace App\Modules\TeachersAuth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name'     => 'required|max:255|min:3',
            'image'    => 'nullable',
        ];
    }
}
