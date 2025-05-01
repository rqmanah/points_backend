<?php

namespace App\Modules\Auth\Models\Schools;

use Illuminate\Foundation\Auth\User as Authenticatable;

class RowsData extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'rows_data';

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

}
