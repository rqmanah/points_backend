<?php

namespace App\Modules\Orders\Resources;

use App\Modules\StudentPrizeOrder\Resources\PrizesResource;
use App\Modules\Students\Resources\StudentsResource;
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
            'id'           => $this->id,
            'student'      => new StudentsResource($this->student),
            'prize'        => $prize_data,
            'status'       => $this->status,
            'price'        => $this->price,
            'completed_at' => $this->completed_at,
            'canceled_at'  => $this->canceled_at,
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at
        ];
    }
}
