<?php

namespace App\Modules\Auth\Models\Grades;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;

class GradesData extends Model
{
    protected $table = 'grades_data';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (GradesData $gradesData) {
            $gradesData->lang_id = Utility::lang_id();
        });

    }

}
