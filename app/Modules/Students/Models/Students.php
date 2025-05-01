<?php

namespace App\Modules\Students\Models;

use App\Bll\Utility;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Students extends Model
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Set connection for tracker package.
     */
    protected $connection = 'mysql';

    protected static function boot()
    {
        parent::boot();
        $school_id = Utility::school_id();
        static::creating(function (Students $user) use ($school_id) {
            $user->guard = 'student';
            $user->school_id = $school_id;
            $user->phone_verified_at = now();
        });

        static::addGlobalScope('ForManager', function (Builder $builder) use ($school_id) {
            $builder->where('guard', 'student')->where('school_id', $school_id);
        });
    }

    public function student(): HasOne
    {
        return $this->hasOne(StudentsExtraData::class, 'user_id');
    }


    public function getImageAttribute($value): string
    {
        return $value ?: 'student_1.png';
    }

    public function behaviors()
    {
        if (Auth::user()?->guard === 'teacher') {
            return $this->hasMany(StudentsBehaviors::class, 'student_id')->where('user_id', Auth::id());
        }
        return $this->hasMany(StudentsBehaviors::class, 'student_id');
    }

    public function getCountGoodAttribute()
    {
        return $this->behaviors()->where('points', '>', 0)->count();
    }

    public function getCountBadAttribute()
    {
        return $this->behaviors()->where('points', '<', 0)->count();
    }

    // get sum points for good points
    public function sumGoodPoints()
    {
        return $this->behaviors()->where('points', '>', 0)->sum('points');
    }

    // get sum of bad points
    public function sumBadPoints()
    {
        return $this->behaviors()->where('points', '<', 0)->sum('points');
    }

    // get sum of all points
    public function sumAllPoints()
    {
        return $this->behaviors()->sum('points');
    }
}
