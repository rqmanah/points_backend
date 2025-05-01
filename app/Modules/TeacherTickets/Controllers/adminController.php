<?php

namespace App\Modules\TeacherTickets\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Modules\TeacherTickets\Requests\StoreTicketRequest;
use App\Modules\TeacherTickets\Services\TicketService;


class adminController extends AdminBaseController
{

    public function __construct()
    {
        $this->service = new TicketService();
        $this->StoreRequest = new StoreTicketRequest();
    }
}
