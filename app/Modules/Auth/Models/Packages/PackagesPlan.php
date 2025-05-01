<?php

namespace App\Modules\Auth\Models\Packages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackagesPlan extends Model
{
    use SoftDeletes;
	protected $table = 'packages_plans';
	protected $guarded = [];

    protected $dates = [
        'start_at',
        'end_at'
    ];
	public $timestamps = true;


}
