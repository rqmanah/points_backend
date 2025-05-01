<?php

namespace App\Modules\StudentPrizeOrder\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $prize_data = '';
        if($this->prize_data !== null && $this->prize_data !== ''){
            $prize_data  = json_decode($this->prize_data, true);
        }

        return [
            'id'            => $this->id,
            'prize_id'      => $this->prize_id,
            'prize'         => $prize_data,
            'status'        => $this->status,
            'price'         => $this->price,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
