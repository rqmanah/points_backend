<?php

namespace App\Modules\TeacherTickets\Services;

use App\Bll\Paths;
use App\Services\Store;
use App\Modules\TeacherTickets\Models\TicketComments;
use App\Modules\TeacherTickets\Resources\TicketCommentResource;

class TicketCommentService extends Store
{
    protected $success;
    public function __construct()
    {
        $this->resource = TicketCommentResource::class;
        $this->saved = __('api.Ticket comment created successfully');
        parent::__construct(new TicketComments());
    }

    public function storeData()
    {
        $this->public_path = Paths::get_public_path('comments');

        $this->store(["message", "ticket_id"], [], "" , "image");
        return $this->saved;
    }

}
