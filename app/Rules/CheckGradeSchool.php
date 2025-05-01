<?php

namespace App\Rules;


use App\Modules\Auth\Models\Grades\Grades;
use App\Modules\Auth\Models\Schools\Schools;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class CheckGradeSchool implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $school_id = Auth::guard('sanctum')?->user()?->school_id;
        $g = Grades::whereHas('Data', function ($query) use ($value) {
            $query->where('title' , $value);
        })->first()?->id;


        if(!$g || !Schools::find($school_id)->grades()->where('grade_id', $g)->exists()){
            $fail("The :attribute doesnt valid for your school.");
        }

    }
}
