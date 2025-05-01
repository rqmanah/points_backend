<?php

namespace App\Modules\Tickets\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Modules\Tickets\Requests\StoreTicketRequest;
use App\Modules\Tickets\Services\TicketService;


class adminController extends AdminBaseController
{

    public function __construct()
    {
        $this->service = new TicketService();
        $this->StoreRequest = new StoreTicketRequest();
    }

     // close ticket
     public function closeTicket($id)
     {
         $data= $this->service->close($id);
         return $this->sendResponse([], $data);
     }
}
