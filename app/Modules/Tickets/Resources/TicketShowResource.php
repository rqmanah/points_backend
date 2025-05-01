<?php

namespace App\Modules\Tickets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user ?
            [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'image' => $this->admin?->image ? asset($this->admin?->image) : null,
            ] : null,
            'comments'   => SimpleTicketCommentResource::collection($this->comments),
            'status'     => $this->status,
            'subject'    => $this->subject,
            'image'      => $this->image,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'closed_at'  => $this->closed_date,
        ];
    }
}
