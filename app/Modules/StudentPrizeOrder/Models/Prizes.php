<?php

namespace App\Modules\StudentPrizeOrder\Models;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Prizes extends Model
{
    use SoftDeletes;

    protected $table = 'prizes';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('schoolOfPrize', function (Builder $builder) {
            $builder->where('school_id', Utility::school_id());
        });

    }

    public function Data(): HasMany
    {
        return $this->hasMany(PrizesData::class, 'prize_id', 'id')->where('lang_id', Utility::lang_id());
    }

}
