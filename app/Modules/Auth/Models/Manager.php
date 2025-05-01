<?php

namespace App\Modules\Auth\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Auth\Models\Schools\Schools;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
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

        static::creating(function (Manager $manager) {
            $manager->guard = 'manager';
        });

        static::addGlobalScope('ForManager', function (Builder $builder) {
            $builder->where('guard', 'manager');
        });

    }


    public function AauthAcessToken(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('\App\OauthAccessToken');
    }
    public function getImageAttribute($value): string
    {
        return $value ?: 'point.png';
    }

    public function hasSchool()
    {
        return $this->hasOne(Schools::class, 'user_id')->exists();
    }

    public function school()
    {
        return $this->hasOne(Schools::class, 'user_id');
    }

}
