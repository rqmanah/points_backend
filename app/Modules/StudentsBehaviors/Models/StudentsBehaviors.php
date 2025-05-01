<?php

namespace App\Modules\StudentsBehaviors\Models;


use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Behaviors\Models\Behaviors;

class StudentsBehaviors extends Model
{

    protected $table = 'students_behaviors';
    protected $guarded = [];
    public $timestamps = true;


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ForStudent', function (Builder $builder) {
            $builder->where('student_id', Auth::guard('sanctum')?->user()?->id)->where('order_id', null);
        });
    }

    public function behavior()
    {
        return $this->belongsTo(Behaviors::class, 'behavior_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
