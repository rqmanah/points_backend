<?php

namespace App\Modules\Students\Models;


use App\Bll\Utility;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use App\Modules\Orders\Models\Orders;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Behaviors\Models\Behaviors;

class StudentsBehaviors extends Model
{

    protected $table = 'students_behaviors';
    protected $guarded = [];
    public $timestamps = true;


    protected static function boot()
    {
        parent::boot();
        static::creating(function (StudentsBehaviors $tabel) {
            $tabel->user_id = Auth::guard('sanctum')?->user()?->id;
            $tabel->school_id = Utility::school_id();

        });
        static::addGlobalScope('school', function ($query) {
            if(Auth::guard('sanctum')?->user()?->guard == 'teacher')
                $query->where('user_id', Auth::guard('sanctum')?->user()?->id);
            else
            $query->where('school_id', Utility::school_id());
        });
    }

    public function behavior()
    {
        return $this->belongsTo(Behaviors::class, 'behavior_id');
    }

    // relation with user
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    // order
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}
