<?php

namespace App\Modules\Auth\Resources\Packages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'free' => $this->free,
            'start_at' => $this->package_started_at,
            'end_at' => $this->package_ended_at,
            'price' => $this->package_plan_price,
            'teachers' => $this->package ?$this->package->Permissions->teachers_count : null,
            'students' => $this->package ? $this->package->Permissions->students_count : null,
            'prizes_count' => $this->package ? $this->package->Permissions->prizes_count : null,
            'title' => $this->package ? isset($this->package->Data->first()->title) ? $this->package->Data->first()->title : '' : null
        ];
    }
}
