<?php

namespace App\Modules\Students\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Auth\Models\Schools\Rows;
use App\Modules\Auth\Models\Grades\Grades;
use App\Modules\Auth\Models\Classes\Classes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StudentsExtraData extends Model
{
    use  SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'students';

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

    public function user()
    {
        return $this->belongsTo(Students::class, 'user_id');
    }

    public function row(): BelongsTo
    {
        return $this->belongsTo(Rows::class, 'row_id');
    }


    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grades::class, 'grade_id');
    }

    public function points(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentsBehaviors::class, 'student_id', 'user_id');
    }

    public function sumPoints(): int
    {
        return $this->points()->sum('points') ?? 0;
    }

    public function sumGoodPoints(): int
    {
        return $this->points()->where('points', '>', 1)->sum('points') ?? 0;
    }

    public function sumBadPoints(): int
    {
        return $this->points()->where('points', '<', 0)->sum('points') ?? 0;
    }

    public function totalPoints(): int
    {
        return $this->points()->where('points', '>', 0)->sum('points') ?? 0;
    }

}
