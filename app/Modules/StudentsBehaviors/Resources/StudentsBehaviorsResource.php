<?php

namespace App\Modules\StudentsBehaviors\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentsBehaviorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'          => $this->id,
            'behavior'    => new BehaviorsResource($this->behavior),
            'points'      => $this->points,
            'note'        => $this->note,
            'user'        => new UserResource($this->user),
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
