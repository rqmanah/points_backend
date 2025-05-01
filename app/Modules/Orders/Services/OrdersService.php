<?php

namespace App\Modules\Orders\Services;

use App\Services\Store;
use App\Modules\Orders\Models\Orders;
use App\Modules\Prizes\Models\Prizes;
use App\Modules\Orders\Resources\OrdersResource;
use App\Modules\Students\Models\StudentsBehaviors;
use App\Modules\Orders\Resources\OrdersResourceEdit;

class OrdersService extends Store
{
    protected $error;
    protected $success;
    protected $saved;

    protected $filter = ['status'];

    public function __construct()
    {
        $this->resource = OrdersResource::class;
        //set messages
        $this->error = __("api.There is no Orders");
        $this->success = __('api.All Orders retrieved successfully');
        $this->saved = __('api.Orders created successfully');

        parent::__construct(Orders::query());
    }

    public function GetAll()
    {
        return $this->Get(["orders.id", "student_id", "prize_id", "school_id", "status", "price", 'orders.created_at'], false);
    }

    public function showData(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Orders::where('id', $id)->first();
            if ($data != null) {
                $this->data = OrdersResourceEdit::make($data);
            }
        }
        return $this->success;
    }

    public function edit(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Orders::where('id', $id)->first();
            if ($data != null) {
                $this->data = OrdersResourceEdit::make($data);
            }
        }
        return $this->success;
    }

    // cancel order
    public function cancel(int $id)
    {
        $order = Orders::find($id);
        if ($order->status === 'compeletd') {
            return __('api.Order is complete');
        }
        if ($order != null) {
            $order->status = 'canceled';
            $order->canceled_at = now();
            $order->save();
            $prize = Prizes::find($order->prize_id);
            $prize->quantity += 1;
            $prize->save();
            $behaviors = StudentsBehaviors::where('order_id', $id)->first();
            if ($behaviors) {
                $behaviors->delete();
            }
            return __('api.Order canceled successfully');
        }
        return __('api.Order not found');
    }

    // compeletd
    public function complete(int $id)
    {
        $order = Orders::find($id);
        if ($order->status === 'canceled') {
            return __('api.Order is canceled');
        }
        if ($order != null) {
            $order->status = 'compeletd';
            $order->completed_at = now();
            $order->save();

            return __('api.Order complete successfully');
        }
        return __('api.Order not found');
    }
}
