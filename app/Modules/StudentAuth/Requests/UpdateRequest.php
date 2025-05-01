<?php

namespace App\Modules\StudentAuth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name'                  => 'required|max:255|min:3',
            'image'                 => 'nullable',
        ];
    }
}
