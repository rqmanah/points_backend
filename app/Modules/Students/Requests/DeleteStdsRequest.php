<?php

namespace App\Modules\Students\Requests;

use App\Modules\Auth\Models\Schools\Schools;
use App\Rules\UserNameCheck;
use App\Rules\CheckRowSchool;
use App\Rules\CheckGradeSchool;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Students\Models\StudentsExtraData;

class DeleteStdsRequest extends FormRequest
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



        return [
            'ids'   => 'required|array',
            'ids.*' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('guard' , 'student')->where(function ($query) {
                    $query->where('school_id', Schools::find(auth()->user()->school_id)->id)->where('deleted_at', null);
                }),
            ],
        ];
    }
}
