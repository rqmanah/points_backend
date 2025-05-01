<?php

namespace App\Modules\Orders\Resources;

use Illuminate\Http\Request;
use App\Modules\Prizes\Resources\PrizesResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Students\Resources\StudentsResource;

class OrdersResourceEdit extends JsonResource
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
            'student'       => new StudentsResource($this->student),
            'prize'         => new PrizesResource($this->prize),
            'status'        => $this->status,
            'price'         => $prize_data,
            'completed_at'  => $this->completed_at,
            'canceled_at'   => $this->canceled_at,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at
        ];
    }
}
