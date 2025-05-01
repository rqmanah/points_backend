<?php

namespace App\Modules\Auth\Models\Schools;
use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;

class SchoolsData extends Model
{
    protected $table = 'schools_data';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (SchoolsData $schoolsData) {
            $schoolsData->lang_id = Utility::lang_id();
        });

    }

}
