<?php

namespace App\Modules\StudentPrizeOrder\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrizesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'        => $this->id,
            'title'     => isset($this->Data->first()->title) ? $this->Data->first()->title : null,
            'price'     => $this->price,
            'order'     => $this->order,
            'min_stock' => $this->min_stock,
            'web_image' => asset($this->image),
            'quantity'  => $this->quantity
        ];
    }
}
