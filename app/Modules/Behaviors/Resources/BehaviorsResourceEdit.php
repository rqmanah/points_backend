<?php

namespace App\Modules\Behaviors\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BehaviorsResourceEdit extends JsonResource
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
            'is_favorite' => $this->is_favorite,
            'admin' => $this->user_id ? false : true

        ];
    }
}
