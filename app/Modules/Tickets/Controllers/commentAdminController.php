<?php

namespace App\Modules\Tickets\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Modules\Tickets\Requests\StoreCommentRequest;
use App\Modules\Tickets\Services\TicketCommentService;


class commentAdminController extends AdminBaseController
{

    public function __construct()
    {
        $this->service = new TicketCommentService();
        $this->StoreRequest = new StoreCommentRequest();
    }

}
