<?php

namespace App\Modules\Auth\Resources\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            'id'                 => $this->id,
            'store_activation'   => $this->store_activation,
            'store_name'         => isset($this->Data->first()->store_name) ? $this->Data->first()->store_name : null,
            'store_message'      => isset($this->Data->first()->store_message) ? $this->Data->first()->store_message : null,
        ];
    }
}
