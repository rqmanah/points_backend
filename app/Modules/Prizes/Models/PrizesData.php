<?php

namespace App\Modules\Prizes\Models;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;

class PrizesData extends Model
{
    protected $table = 'prizes_data';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (PrizesData $behaviorsData) {
            $behaviorsData->lang_id = Utility::lang_id();
        });

    }

}
