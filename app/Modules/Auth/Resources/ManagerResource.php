<?php

namespace App\Modules\Auth\Resources;

use App\Modules\Auth\Resources\Packages\SchoolPackageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'dialing_code' => $this->dialing_code,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'phone_verified_at' => $this->phone_verified_at,
            'token' => $this->token,
            'guard' => $this->guard,
            'national_id' => $this->national_id,
            'schools' => new SchoolsResource($this->school),
            'school_logo' => $this->school?->image ? asset($this->school?->image) : null,
            'has_school'  => $this->guard !== 'manager' ? true : $this->hasSchool(),
            'has_package' => $this->guard !== 'manager' ? true : ($this->school ? ($this->school->schoolPackage ? true : false) : false),
            'packages' => $this->school ? SchoolPackageResource::make($this->school->schoolPackage) : null,
            'image' => $this->image ?? null,
        ];
    }
}
