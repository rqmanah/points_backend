<?php

namespace App\Modules\Behaviors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FavouriteBehaviors extends Model
{

    protected $table = 'favourite_behaviors';
    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        $user_id = Auth::guard('sanctum')?->user()?->id;
        parent::boot();
        static::addGlobalScope('schoolOfBehavior', function (Builder $builder) use ($user_id) {
            $builder->where('user_id', $user_id);
        });

        static::creating(function (FavouriteBehaviors $FavouriteBehaviors) use ($user_id) {
            $FavouriteBehaviors->user_id = $user_id;
        });

    }


}
