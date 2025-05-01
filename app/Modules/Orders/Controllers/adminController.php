<?php

namespace App\Modules\Orders\Controllers;

use Illuminate\Http\Request;
use App\Modules\Orders\Models\Orders;
use App\Http\Controllers\AdminBaseController;
use App\Modules\Orders\Services\OrdersService;
use App\Modules\Orders\Resources\OrdersResource;

class adminController extends AdminBaseController
{
    public function __construct()
    {
        $this->service = new OrdersService();
    }

    // get all orders
    public function index()
    {
        $data = Orders::with('student', 'prize')->get();
        // search by student name by term
        if(request()?->has('term') && request()->term != ''){
            $data = Orders::whereHas('student', function($q){
                $q->where('name', 'like', '%' . request()->term . '%');
            })->get();

        }
        return $this->sendResponse(OrdersResource::collection($data), __('api.All Orders'));
    }

    // cancel order
    public function cancel(Request $request)
    {
        $msg = $this->service->cancel($request->id);
        return $this->sendResponse([], $msg);
    }
    // complete order
    public function complete(Request $request)
    {
        $msg = $this->service->complete($request->id);
        return $this->sendResponse([], $msg);
    }
}
