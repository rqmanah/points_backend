<?php

namespace App\Modules\Reports\Models;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AddedBehaviours extends Model
{

    protected $table = 'students_behaviors';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('schoolOfPrize', function (Builder $builder) {
            $builder->where('school_id', Utility::school_id());
        });

    }

}
