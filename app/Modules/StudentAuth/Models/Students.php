<?php

namespace App\Modules\StudentAuth\Models;

use App\Modules\Auth\Models\Schools\Schools;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Students extends Authenticatable
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

        static::addGlobalScope('ForStudent', function (Builder $builder) {
            $builder->where('guard', 'student');
        });
    }

    public function getImageAttribute($value): string
    {
        return $value ?: 'student_1.png';
    }

    public function student(): HasOne
    {
        return $this->hasOne(StudentsExtraData::class, 'user_id');
    }

    public function school(): HasOne
    {
        return $this->hasOne(Schools::class, 'id', 'school_id');
    }

    public function schools()
    {
        return $this->hasOne(Schools::class, 'id', 'school_id');
    }
}
