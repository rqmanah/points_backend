<?php

namespace App\Rules;

use App\Bll\Utility;
use App\Models\Users;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneStore implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phone = (new Utility)->removeZeroFomphone($value);
        $query = Users::query();
        if ($query->where('phone', $phone)->exists()) {
            $fail("The :attribute has already been taken.");
        }
    }
}
