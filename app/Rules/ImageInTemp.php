<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class ImageInTemp implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = public_path(DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $value);
        if (file_exists($file)) {

            $validator = Validator::make([$file => $value], [
                $file => 'file|mimes:png,jpg,jpeg,gif|max:2048',
            ]);
            if ($validator->fails()) {
                $fail("The file :attribute must be an Excel file ( png,jpg,jpeg or gif).");
            }
        } else {
            $fail("The file :attribute does not exist.");
        }
    }
}
