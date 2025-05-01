<?php

namespace App\Modules\ManagerReport\Models;


use App\Bll\Utility;
use App\Modules\Behaviors\Models\Behaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StudentsBehaviors extends Model
{

    protected $table = 'students_behaviors';
    protected $guarded = [];
    public $timestamps = true;


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('StudentsBehaviors', function (Builder $builder) {
            $builder->where('school_id', Utility::school_id());
        });
    }

    public function behavior()
    {
        return $this->belongsTo(Behaviors::class, 'behavior_id');
    }
}
