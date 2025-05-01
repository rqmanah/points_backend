<?php

namespace App\Modules\TeacherTickets\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Modules\TeacherTickets\Requests\StoreCommentRequest;
use App\Modules\TeacherTickets\Services\TicketCommentService;


class commentAdminController extends AdminBaseController
{

    public function __construct()
    {
        $this->service = new TicketCommentService();
        $this->StoreRequest = new StoreCommentRequest();
    }

}
