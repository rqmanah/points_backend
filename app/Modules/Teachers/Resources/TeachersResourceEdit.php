<?php

namespace App\Modules\Teachers\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeachersResourceEdit extends JsonResource
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
            'is_active' => $this->is_active,
            'national_id' => $this->national_id,
        ];
    }
}
