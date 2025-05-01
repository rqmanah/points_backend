<?php

namespace App\Modules\TeachersAuth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'gender' => $this->gender,
            'is_active' => $this->is_active,
            'image' => $this->image,
            'national_id' => $this->national_id,
            'token' => $this->token ?? null,

        ];
    }
}
