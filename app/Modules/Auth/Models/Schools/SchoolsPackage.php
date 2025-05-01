<?php

namespace App\Modules\Auth\Models\Schools;

use App\Modules\Auth\Models\Packages\Packages;
use App\Modules\Prizes\Models\Prizes;
use App\Modules\StudentAuth\Models\Students;
use App\Modules\Teachers\Models\Teachers;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SchoolsPackage extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'school_packages';

    protected $guarded = [];

    protected $casts = [
        'package_ended_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    /**
     * Set connection for tracker package.
     */
    protected $connection = 'mysql';

    public function package()
    {
        return $this->belongsTo(Packages::class, 'package_id', 'id');
    }

    public function permissionX()
    {
        $packageStudentsCount = Auth::guard('sanctum')?->user()?->school->schoolPackage->package?->Permissions?->students_count - Students::where('school_id', Auth::guard('sanctum')?->user()?->school_id)->count();
        $packageTeachersCount = Auth::guard('sanctum')?->user()?->school->schoolPackage->package?->Permissions?->teachers_count - Teachers::where('school_id', Auth::guard('sanctum')?->user()?->school_id)->count();
        $packagePrizesCount = Auth::guard('sanctum')?->user()?->school->schoolPackage->package?->Permissions?->prizes_count - Prizes::where('school_id', Auth::guard('sanctum')?->user()?->school_id)->count();
        $packageEndedAt = Auth::guard('sanctum')?->user()?->school->schoolPackage->package_ended_at;
        $timeDifference = $packageEndedAt->diffInSeconds(now());
        $timeDifference = $timeDifference / (60 * 60 * 24);
        return [
            'students' => $packageStudentsCount,
            'teachers' => $packageTeachersCount,
            'prizes' => $packagePrizesCount,
            'ended_at_by_days' => (int)$timeDifference
        ];
    }

}
