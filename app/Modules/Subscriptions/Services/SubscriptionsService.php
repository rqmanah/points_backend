<?php

namespace App\Modules\Subscriptions\Services;

use App\Modules\Auth\Models\Schools\SchoolsPackage;
use App\Modules\Prizes\Models\Prizes;
use App\Modules\StudentAuth\Models\Students;
use App\Services\Store;
use App\Modules\Subscriptions\Resources\SubscriptionsResource;
use App\Modules\Teachers\Models\Teachers;
use Illuminate\Support\Facades\Auth;

class SubscriptionsService extends Store
{
    protected $error;
    protected $success;
    protected $saved;

    public function __construct()
    {
        $this->resource = SubscriptionsResource::class;
        //set messages
        $this->error = __('api.There is no subscriptions');
        $this->success = __('api.All subscriptions retrieved successfully');
        $this->saved = __('api.Subscriptions created successfully');

        parent::__construct(SchoolsPackage::where('school_id', Auth::guard('sanctum')->user()?->school_id));
    }

    public function GetAll()
    {
        if (!Auth::guard('sanctum')->user()->school_id) {
            return [];
        }
        return $this->Get(
            [
                "school_packages.id",
                "package_id",
                "package_started_at",
                "package_ended_at",
                "school_id",
                "is_active",
                "free",
                "package_plan_price",
                "created_at",
                "coupon"
            ],
            []
        );
    }

    public function showData(int $id)
    {
        if (!Auth::guard('sanctum')->user()->school_id) {
            return [];
        }
        if (isset($id) && is_int($id)) {
            $data = SchoolsPackage::where('school_id', Auth::guard('sanctum')->user()->school_id)->where('id', $id)->first();
            if ($data != null) {
                $this->data = SubscriptionsResource::make($data);
            }
        }
        return __('api.Subscriptions retrieved successfully');
    }

    public function availablePermissions()
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
