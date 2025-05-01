<?php

namespace App\Modules\Auth\Resources\Packages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagesPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'price'             => $this->price,
            'after_price'             => $this->after_price,
            'start_at'          => $this->start_at ? substr($this->start_at, 0, 10) : null,
            'end_at'            => $this->end_at ? substr($this->end_at, 0, 10) : null,
        ];
    }
}
