<?php

namespace App\Modules\StudentsBehaviors\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'user_name'     => $this->user_name,
            "email"         => $this->email,
            "dialing_code"  => $this->dialing_code,
            "phone"         => $this->phone,
            "gender"        => $this->gender,
            "guard"         => $this->guard,
        ];
    }
}
