<?php

namespace App\Modules\Students\Requests;

use App\Bll\Utility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddBehaviorRequest extends FormRequest
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
            'behavior_id' => [
                'required',
                'integer',
                Rule::exists('behaviors', 'id')->where(function ($query) {
                    $query->where('user_id', Auth::guard('sanctum')?->user()?->id)
                        ->orWhere('user_id', null)
                        ->whereNull('deleted_at');
                }),
            ],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('school_id', Utility::school_id())
                        ->whereNull('deleted_at')
                        ->where('guard', 'student');
                }),
            ],
            'note' => 'nullable|string|max:255',
        ];
    }
}
