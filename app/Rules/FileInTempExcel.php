<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class FileInTempExcel implements ValidationRule
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
                $file => 'file|mimes:xlsx,xls',
            ]);
            if ($validator->fails()) {
                $fail("The file :attribute must be an Excel file (xlsx or xls).");
            }
        } else {
            $fail("The file :attribute does not exist.");
        }
    }
}
