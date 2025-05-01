<?php

namespace App\Modules\Auth\Models\Classes;
use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use SoftDeletes;
	protected $table = 'classes';
	protected $guarded = [];
	public $timestamps = true;


    public function Data(): HasMany
    {
        return $this->hasMany(ClassesData::class, 'class_id', 'id')->where('lang_id', Utility::lang_id());
    }






}
