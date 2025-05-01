<?php

namespace App\Modules\Auth\Models\Schools;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Rows extends Authenticatable
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'rows';

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
        return $this->hasMany(RowsData::class, 'row_id', 'id')->where('lang_id', Utility::lang_id());
    }

}
