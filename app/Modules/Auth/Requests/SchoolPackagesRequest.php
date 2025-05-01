<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolPackagesRequest extends FormRequest
{

    public function rules()
    {
        return [
            'package_id' => 'required|integer|exists:packages,id,deleted_at,NULL',
            'coupon' => 'nullable|string'
        ];
    }
}
