<?php

namespace App\Modules\Teachers\Requests;

use App\Modules\Auth\Models\Schools\Schools;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteTeachersRequest extends FormRequest
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
                Rule::exists('users', 'id')->where('guard' , 'teacher')->where(function ($query) {
                    $query->where('school_id', Schools::find(auth()->user()->school_id)->id)->where('deleted_at', null);
                }),
            ],
        ];
    }
}
