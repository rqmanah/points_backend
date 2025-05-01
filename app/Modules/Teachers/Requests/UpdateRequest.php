<?php

namespace App\Modules\Teachers\Requests;

use App\Rules\UserNameCheck;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $teacer =  request()->route()->parameter('teacher');

        return [
            'name'      => 'required|string|max:255|min:3',
            'user_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                new UserNameCheck,
                'unique:users,user_name,' . $teacer

            ],
            'national_id' => [
                'nullable',
                'digits_between:5,25',
                'unique:users,national_id,' . $teacer
            ],
            'is_active' => 'required|boolean'
        ];
    }
}
