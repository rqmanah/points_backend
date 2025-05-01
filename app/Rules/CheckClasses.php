<?php

namespace App\Rules;

use App\Modules\Auth\Models\Classes\Classes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckClasses implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $c = Classes::with(['Data' => function ($query) use ($value) {
            $query->where('title', 'like', '%' . $value . '%');
        }])->first()->id;

        if (!$c) {
            $fail("The :attribute doesnt valid for your school.");
        }

    }
}
