<?php

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountriesResource extends JsonResource
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
            'code'        => $this->code,
            'active'      => $this->active,
            'name'        => isset($this->Data->first()->name) ? $this->Data->first()->name : null,
        ];
    }
}
