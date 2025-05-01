<?php

namespace App\Modules\Auth\Models\Packages;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Packages extends Model
{
    use SoftDeletes;
    protected $table = 'packages';
    protected $guarded = [];
    public $timestamps = true;


    protected static function boot()
    {
        parent::boot();



        static::addGlobalScope('ForManager', function (Builder $builder) {
            $builder->whereHas('Plans', function ($query) {
                $query->where('end_at', '>=', now());
            });
        });

    }

    public function Data(): HasMany
    {
        return $this->hasMany(PackagesData::class, 'package_id', 'id')->where('lang_id', Utility::lang_id());
    }

    public function Plans(): HasOne
    {
        return $this->hasOne(PackagesPlan::class, 'package_id', 'id');
    }

    public function Permissions(): HasOne
    {
        return $this->hasOne(PackagesPermission::class, 'package_id');
    }



}
