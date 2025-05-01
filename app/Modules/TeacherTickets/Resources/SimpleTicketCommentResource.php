<?php

namespace App\Modules\TeacherTickets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleTicketCommentResource extends JsonResource
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
            'message'    => $this->message,
            'manager_id' => $this->manager_id,
            'admin_id'   => $this->admin_id,
            'web_image'  => $this->image ? asset($this->image) : null,
            'ticket_id'  => $this->ticket_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
