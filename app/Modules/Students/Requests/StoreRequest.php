<?php

namespace App\Modules\Students\Requests;

use App\Rules\PasswordComplexity;
use App\Rules\UserNameCheck;
use App\Rules\CheckRowSchool;
use App\Rules\CheckGradeSchool;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Auth\Models\Schools\Schools;

class StoreRequest extends FormRequest
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
            'name'      => 'required|string|max:255|min:3',
            'grade_id' => [
                'required', 'integer', 'exists:grades,id',
                Rule::exists('schools_grade', 'grade_id')->where(function ($query) {
                    $query->where('school_id', Schools::find(auth()->user()->school_id)->id);
                }),
            ],
            'row_id'         => ['required', 'integer', 'exists:rows,id'],
            'class_id'       => 'required|integer|exists:classes,id',
            'user_name'      => ['nullable', 'string', 'max:255', 'min:3', new UserNameCheck , 'unique:users,user_name'],
            'national_id'    => 'nullable|unique:users,national_id|digits_between:5,15',
            'is_active'      => 'required|boolean',
            'password'       => ['required','string','min:6','max:255', new PasswordComplexity],
            'guardian_phone' => 'required|string',
            'dialing_code'   => 'required|string',
        ];
    }
}
