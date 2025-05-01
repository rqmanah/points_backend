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

        $user_id = request()->route('student');

        return [
            'name' => 'required|string|max:255|min:3',
            'user_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                new UserNameCheck,
                Rule::unique('users', 'user_name')->ignore(request()->route('student')),
            ],

            'grade_id' => [
                'required',
                'integer',
                'exists:grades,id',
                Rule::exists('schools_grade', 'grade_id')->where(function ($query) {
                    $query->where('school_id', Schools::find(auth()->user()->school_id)->id);
                }),
            ],
            'row_id' => ['required', 'integer', 'exists:rows,id'],
            'class_id' => 'required|integer|exists:classes,id',
            'national_id' => [
                'nullable',
                'digits_between:5,25',
                'numeric',
                Rule::unique('users', 'national_id')->ignore(request()->route('student')),
            ],
            'is_active' => 'required|boolean',
            'guardian_phone' => 'required|string',
            'dialing_code' => 'required|string',
        ];
    }
}
