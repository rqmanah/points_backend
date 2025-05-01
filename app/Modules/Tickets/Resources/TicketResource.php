<?php

namespace App\Modules\Tickets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'subject'    => $this->subject,
            'image'      => $this->image,
            'closed_at'  => $this->closed_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
