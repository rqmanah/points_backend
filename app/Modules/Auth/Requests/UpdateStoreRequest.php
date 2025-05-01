<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{

    public function rules()
    {
        return [
            'store_activation'  => 'required|boolean',
            'store_name'        => 'required|string|max:255',
            'store_message'     => 'required|string',
        ];
    }
}
