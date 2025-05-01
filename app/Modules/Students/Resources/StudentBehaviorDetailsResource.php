<?php

namespace App\Modules\Students\Resources;

use App\Modules\Behaviors\Resources\SimpleBehaviorsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentBehaviorDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'behavior'   => SimpleBehaviorsResource::make($this->behavior),
            'user'       => new SimpleUserResource($this->user),
            'points'     => $this->points,
            'note'       => $this->note,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
