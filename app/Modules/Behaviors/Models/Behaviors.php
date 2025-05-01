<?php

namespace App\Modules\Behaviors\Models;

use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Behaviors extends Model
{
    use SoftDeletes;

    protected $table = 'behaviors';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        $user_id = Auth::guard('sanctum')?->user()?->id;
        parent::boot();
        static::addGlobalScope('schoolOfBehavior', function (Builder $builder) use ($user_id) {
            $builder->where('user_id', $user_id)
                ->orWhere('user_id', null);
        });

        static::creating(function (Behaviors $behaviors) use ($user_id) {
            $behaviors->school_id = Utility::school_id();
            $behaviors->user_id = $user_id;
        });

    }

    public function Data(): HasMany
    {
        return $this->hasMany(BehaviorsData::class, 'behavior_id', 'id')->where('lang_id', Utility::lang_id());
    }

    // favouriteBehaviors
    public function favouriteBehaviors(): HasMany
    {
        return $this->hasMany(FavouriteBehaviors::class, 'behavior_id', 'id');
    }

    // get is favorite
    public function getIsFavoriteAttribute()
    {
        return $this->favouriteBehaviors()->where('user_id', Auth::guard('sanctum')?->user()?->id)->exists();
    }


}
