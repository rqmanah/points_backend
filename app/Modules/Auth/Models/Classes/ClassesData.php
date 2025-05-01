<?php

namespace App\Modules\Auth\Models\Classes;
use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;

class ClassesData extends Model
{
    protected $table = 'classes_data';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (ClassesData $ClassesData) {
            $ClassesData->lang_id = Utility::lang_id();
        });

    }

}
