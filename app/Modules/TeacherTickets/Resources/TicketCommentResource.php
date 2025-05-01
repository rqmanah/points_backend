<?php

namespace App\Modules\TeacherTickets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketCommentResource extends JsonResource
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
            'manager'    => $this->manager ?
                [
                    'id'    => $this->manager?->id,
                    'name'  => $this->manager?->name,
                    'image' => $this->admin?->image ? asset($this->admin?->image) : null,
                    'guard' => $this->manager?->guard,
                ] : null,
            'admin_id' => $this->admin_id,
            'admin'    => $this->admin ?
                [
                    'id'    => $this->admin?->id,
                    'name'  => $this->admin?->name,
                    'image' => $this->admin?->image ? asset($this->admin?->image) : null,
                ] : null,

            'ticket_id'  => $this->ticket_id,
            'ticket'     => new TicketResource($this->ticket),
            'web_image'  => $this->image ? asset($this->image) : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),

        ];
    }
}
