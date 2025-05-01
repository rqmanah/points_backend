<?php

namespace App\Modules\StudentsBehaviors\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BehaviorsResource extends JsonResource
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
            'points' => $this->points,
            'title' => isset($this->Data->first()->title) ? $this->Data->first()->title : null,
        ];
    }
}
