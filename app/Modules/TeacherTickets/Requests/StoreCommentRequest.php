<?php

namespace App\Modules\TeacherTickets\Requests;

use App\Bll\Utility;
use App\Rules\ImageInTemp;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'message' => 'required|string',
            'ticket_id' => [
                'required',
                'integer',
                Rule::exists('tickets', 'id')->where(function ($query) {
                    $query->where('school_id', Utility::school_id())->where('status', 'opened');
                })
            ],
            'image' => ['nullable', 'string', 'max:255', new ImageInTemp]

        ];
    }
}
