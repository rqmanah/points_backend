<?php

namespace App\Modules\Auth\Models\Grades;
use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grades extends Model
{
    use SoftDeletes;
	protected $table = 'grades';
	protected $guarded = [];
	public $timestamps = true;


    public function Data(): HasMany
    {
        return $this->hasMany(GradesData::class, 'grade_id', 'id')->where('lang_id', Utility::lang_id());
    }




}
