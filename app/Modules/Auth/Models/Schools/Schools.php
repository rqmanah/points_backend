<?php

namespace App\Modules\Auth\Models\Schools;

use App\Bll\Utility;
use App\Modules\Auth\Models\Classes\Classes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Modules\Auth\Models\Grades\Grades;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Auth\Models\Packages\Packages;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Schools extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'schools';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    /**
     * Set connection for tracker package.
     */
    protected $connection = 'mysql';

    public function Data(): HasMany
    {
        return $this->hasMany(SchoolsData::class, 'school_id', 'id')->where('lang_id', Utility::lang_id());
    }

    public function grades()
    {
        return $this->belongsToMany(Grades::class, 'schools_grade', 'school_id', 'grade_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Packages::class, 'school_packages', 'school_id', 'package_id');
    }

    public function schoolPackage()
    {
        // get active package for the school
        return $this->hasOne(SchoolsPackage::class, 'school_id', 'id')->where('package_ended_at', '>', now());
    }

    public function classes()
    {
        return Classes::all();
    }

    public function hasPackage(): bool
    {
        // check if the school has a package free
        if (SchoolsPackage::where('school_id', $this->id)->where('package_ended_at', '>', now())->where('free', 1)->count() > 0) {
            return true;
        }
        return $this->packages()->where('package_ended_at', '>', now())->count() > 0;
    }
}
