<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolCouponCheck extends FormRequest
{

    public function rules()
    {
        return [
            'coupon' => 'required|string',
        ];
    }
}
