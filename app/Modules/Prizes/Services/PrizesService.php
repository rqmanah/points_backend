<?php

namespace App\Modules\Prizes\Services;

use App\Bll\Paths;
use App\Bll\Utility;
use App\Modules\Auth\Models\Schools\Schools;
use App\Services\Store;
use App\Modules\Prizes\Models\Prizes;
use App\Modules\Prizes\Resources\PrizesResource;
use App\Modules\Prizes\Resources\PrizesResourceEdit;
use Illuminate\Support\Facades\DB;

class PrizesService extends Store
{
    protected $error;
    protected $success;
    protected $saved;
    protected $custom_filter = ["stock" => "getStock"];

    public function __construct()
    {
        $this->resource = PrizesResource::class;
        //set messages
        $this->error = __('api.No Prizes');
        $this->success = __('api.All Prizes retrieved successfully');
        $this->saved = __('api.Prizes created successfully');

        parent::__construct(Prizes::query());
    }

    public function getStock()
    {
        $school_id = Utility::school_id();
        $school = Schools::where('id', $school_id)->first();
        $data = Prizes::query();

        if (request()->has('filter')) {
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

        $result = $data->with('Data')->orderBy('price', 'desc')->get();

        return PrizesResource::collection($result);
    }


    public function GetAll()
    {
        return $this->Get(["prizes.id", "title", "order", "image", "price", "quantity", "min_stock"], ['table' => 'prizes_data', 'key' => 'prize_id']);
    }

    public function storeData()
    {
        if (!Utility::checkPrizesCount()) {
            return __('api.You have reached the maximum number of prizes');
        }
        try {
            DB::beginTransaction();
            $order = $this->getLatestNumber();
            request()?->merge(['order' => $order]);
            $this->public_path = Paths::get_public_path('prizes');
            $this->store(["price", "order", "quantity", "min_stock"], ["title"], "prize_id", "image");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->saved;
    }

    public function showData(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Prizes::whereHas('Data')->where('id', $id)->first();
            if ($data != null) {
                $this->data = PrizesResourceEdit::make($data);
            }
        }
        return $this->saved;
    }

    public function edit(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Prizes::where('id', $id)->whereHas('Data')->first();
            if ($data != null) {
                $this->data = PrizesResourceEdit::make($data);
            }
        }
        return $this->saved;
    }

    public function updateData(int $id)
    {
        $this->sortPrize($id , request()->order);
        $this->public_path = Paths::get_public_path('prizes');
        $this->update(["price", "min_stock", 'order' , "quantity"], ["title"], "prize_id", $id, "image");
        return $this->saved;
    }

    public
    function deleteData(int $id)
    {
        $this->delete($id);
    }

    protected function getLatestNumber()
    {
        $faq = Prizes::orderBy('order', 'desc');
        $faq->limit(1);
        $data = $faq->first() ?? (object)['order' => 0];
        return $data->order + 1;
    }

    public function sortPrize($id , $order)
    {
        $model = new Prizes();
        $model::findOrFail($id);

        DB::transaction(function () use ($order, $id) {
            Prizes::where('id', $id)->update([
                'order' => $order,
            ]);
            Prizes::where('id', '!=', $id)
                ->where('order', '>=', $order)
                ->increment('order');
        });
        return __('api.Prize sorted successfully');
    }
}
