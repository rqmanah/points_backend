<?php

namespace App\Modules\Orders\Models;

use App\Bll\Utility;
use App\Modules\Prizes\Models\Prizes;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Students\Models\Students;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $guarded = [];
    public $timestamps = true;


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('schoolOfPrize', function (Builder $builder) {
            $builder->where('school_id', Utility::school_id())->where('canceled_by_user', 0);
        });

    }

    // relation with a student
    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    // prize relation
    public function prize()
    {
        return $this->belongsTo(Prizes::class, 'prize_id');
    }

}
