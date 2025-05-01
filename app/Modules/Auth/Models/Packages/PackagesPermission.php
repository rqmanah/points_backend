<?php

namespace App\Modules\Auth\Models\Packages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackagesPermission extends Model
{
    use SoftDeletes;
	protected $table = 'package_permissions';
	protected $guarded = [];
	public $timestamps = true;

}
