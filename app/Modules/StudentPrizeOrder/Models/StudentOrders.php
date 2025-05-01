<?php

namespace App\Modules\StudentPrizeOrder\Models;

use App\Bll\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentOrders extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('StudentOrders', function (Builder $builder) {
            $builder->where('school_id', Utility::school_id());
            $builder->where('student_id', Auth::guard('sanctum')?->user()?->id);
        });

        static::creating(function (StudentOrders $behaviors) {
            $behaviors->school_id  = Utility::school_id();
            $behaviors->student_id = Auth::guard('sanctum')?->user()?->id;
        });

    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prizes::class, 'prize_id', 'id');
    }


}
