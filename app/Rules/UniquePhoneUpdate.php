<?php

namespace App\Rules;

use App\Bll\Utility;
use App\Models\Users;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneUpdate implements ValidationRule
{
    protected $oldPhone;
    protected $user_id;

    public function __construct($oldPhone , $user_id)
    {
        $this->oldPhone = $oldPhone;
        $this->user_id = $user_id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phone = (new Utility)->removeZeroFomphone($value);
        $query = Users::query();
        if ($query->where('phone', $this->oldPhone)->where('id', '!=', $this->user_id)->exists()) {
            $fail("The :attribute has already been taken.");
        }
    }
}
