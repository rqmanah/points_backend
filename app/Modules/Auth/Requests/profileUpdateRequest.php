<?php

namespace App\Modules\Auth\Requests;

use App\Rules\UserNameCheck;
use Illuminate\Validation\Rule;
use App\Rules\UniquePhoneUpdate;
use App\Rules\PasswordComplexity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class profileUpdateRequest extends FormRequest
{



    public function rules()
    {

        $user   = Auth::guard('sanctum')->user();
        $user_id    = $user->id;
        $user_phone = $user->phone;

        return [
            'name' => 'required|string|max:255|min:3',
            'user_name' => [
                'required',
                'string',
                'max:15',
                'min:3',
                Rule::unique('users')->ignore($user_id),
                new UserNameCheck
            ],
            'dialing_code' => 'required|string|max:5',
            'phone' => [
                'required',
                'digits_between:5,15',
                'max:15',
                new UniquePhoneUpdate($user_phone, $user_id),
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email,' . auth()->id()
            ],
            'gender' => 'required|in:male,female',
            'national_id' => 'nullable|numeric|digits_between:5,15|unique:users,national_id,' . auth()->id(),
            'password'    =>  ['nullable', 'string', 'min:6' , 'confirmed' , new PasswordComplexity],

        ];
    }
}
