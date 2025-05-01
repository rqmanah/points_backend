<?php

namespace App\Modules\TeachersAuth\Models;


use App\Modules\Auth\Models\Schools\Schools;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Teacher extends Authenticatable
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



    public function AauthAcessToken(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function getImageAttribute($value): string
    {
        return $value ?: 'teacher_1.png';
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
