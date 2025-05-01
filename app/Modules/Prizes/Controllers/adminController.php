<?php

namespace App\Modules\Prizes\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Modules\Prizes\Requests\UpdateRequest;
use App\Modules\Prizes\Requests\StoreRequest;
use App\Modules\Prizes\Services\PrizesService;
use Illuminate\Http\Request;

class adminController extends AdminBaseController
{
    public function __construct()
    {
        $this->service = new PrizesService();
        $this->StoreRequest = new StoreRequest();
        $this->UpdateRequest = new UpdateRequest();
    }

    // get Stock
    public function getStock(): \Illuminate\Http\JsonResponse
    {
        $msg = $this->service->getStock();
        return $this->sendResponse($msg , __('api.Stock List'));
    }

}
