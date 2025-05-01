<?php

namespace App\Modules\Auth\Models\Packages;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;

class PackagesData extends Model
{
    protected $table = 'packages_data';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (PackagesData $packagesData) {
            $packagesData->lang_id = Utility::lang_id();
        });

    }

}
