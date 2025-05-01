<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolRequest extends FormRequest
{

    public function rules()
    {
        return [
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'address'        => 'required|string|max:255',
            'grades_id'      => 'required|array',
            'grades_id.*'    => 'required|integer|exists:grades,id',
            'type'           => 'required|in:governmental,private',
            'gender'         => 'required|in:boys,girls,mixed',
            'image'          => ['nullable', 'string', 'max:255'],
            'country_id'     => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('active', 1)
            ],
        ];
    }
}
