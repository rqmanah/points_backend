<?php

namespace App\Modules\StudentPrizeOrder\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Models\Schools\Schools;
use App\Modules\StudentAuth\Models\Students;
use App\Modules\StudentPrizeOrder\Models\Prizes;
use App\Modules\Auth\Resources\Store\StoreResource;
use App\Modules\StudentPrizeOrder\Models\StudentOrders;
use App\Modules\StudentPrizeOrder\Requests\OrderRequest;
use App\Modules\StudentPrizeOrder\Models\StudentsBehaviors;
use App\Modules\StudentPrizeOrder\Resources\OrdersResource;
use App\Modules\StudentPrizeOrder\Resources\PrizesResource;

class adminController extends Controller
{

    // get store data
    public function schoolStoreData()
    {
        $school_id = Auth::guard('sanctum')?->user()?->school_id;
        $school = Schools::where('id', $school_id)->with('Data')->first();
        if (!$school) {
            return $this->sendError(__('api.School not found'));
        }
        return $this->sendResponse(new StoreResource($school), __('api.Store Data'));
    }

    public function prizes()
    {
        $school_id = Auth::guard('sanctum')?->user()?->school_id;
        $school = Schools::where('id', $school_id)->first();
        if (!$school) {
            return $this->sendError(__('api.School not found'));
        }
        if($school->store_activation !== 1){
            return $this->sendError(__('api.Store is not activated'));
        }
        $data = Prizes::query();

        if (request()?->has('filter')) {
            $filter = request('filter')['stock'];


            if ($filter === 'mini') {
                $data->whereColumn('quantity', '<=', 'min_stock')->where('quantity', '>', 0);
            }
            if ($filter === 'max') {
                $data->whereColumn('quantity', '>', 'min_stock');
            }
            if ($filter === 'empty') {
                $data->where('quantity', 0);
            }
        }

        // order by price
        $result = $data->with('Data')->orderBy('price', 'desc')->get();

        return $this->sendResponse(PrizesResource::collection($result), __('api.Prizes List'));
    }
    public function createOrder(OrderRequest $request)
    {
        $prize    = Prizes::where('id', $request->prize_id)->first();
        $user_id  = Auth::guard('sanctum')?->user()?->id;
        $student  = Students::where('id', $user_id)->first();

        if ($prize->quantity <= 0) {
            return $this->sendError(__('api.Stock is not enough'));
        }
        if ($student && $student?->student?->sumPoints() < $prize->price) {
            return $this->sendError(__('api.You do not have enough points'));
        }

        DB::beginTransaction();
        try {
            $prize_data = [
                'id'        => $prize->id,
                'title'     => isset($prize->Data->first()->title) ? $prize->Data->first()->title : null,
                'price'     => $prize->price,
                'web_image' => $prize->image ? asset($prize->image) : null,
            ];
            $order = StudentOrders::create([
                'student_id' => $student->id,
                'prize_id'   => $request->prize_id,
                'school_id'  => $student->school_id,
                'status'     => 'pending',
                'prize_data' => json_encode($prize_data),
                'price'      => $prize->price,
            ]);
            $prize->quantity -= 1;
            $prize->save();
            StudentsBehaviors::create([
                'student_id' => $order->student_id,
                'points'     => $prize->price * -1,
                'school_id'  => $order->school_id,
                'prize_id'   => $prize->id,
                'order_id'   => $order->id,
            ]);
            DB::commit();
            return $this->sendResponse([], __('api.Order has been successfully registered'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
    public function cancelOrder($id)
    {
        $order = StudentOrders::where('id', $id)->first();
        if ($order && $order->status === 'pending') {
            $order->status = 'canceled';
            $order->canceled_by_user = 1;
            $order->save();
            $studentBehavior = StudentsBehaviors::where('order_id', $id)->first();
            if ($studentBehavior) {
                $studentBehavior->delete();
            }
            return $this->sendResponse([], __('api.Order has been successfully canceled'));
        }
        return $this->sendError(__('api.Order not found'));
    }
    public function listMyOrders()
    {
        $orders = StudentOrders::with('prize')->get();
        return $this->sendResponse(OrdersResource::collection($orders), __('api.My Orders'));
    }
}
