<?php

namespace App\Modules\Tickets\Requests;

use App\Rules\ImageInTemp;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'subject' => 'required|string',
            'message' => 'required|string',
            'image'   => ['nullable','string','max:255' , new ImageInTemp],
        ];
    }

}
