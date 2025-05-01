<?php

namespace App\Modules\Behaviors\Models;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;

class BehaviorsData extends Model
{
    protected $table = 'behaviors_data';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (BehaviorsData $behaviorsData) {
            $behaviorsData->lang_id = Utility::lang_id();
        });

    }

}
