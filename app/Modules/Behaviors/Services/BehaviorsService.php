<?php

namespace App\Modules\Behaviors\Services;

use App\Bll\Utility;
use App\Services\Store;
use App\Modules\Behaviors\Models\Behaviors;
use App\Modules\Behaviors\Resources\BehaviorsResource;
use App\Modules\Behaviors\Resources\BehaviorsResourceEdit;

class BehaviorsService extends Store
{

    protected $error;
    protected $success;
    protected $saved;
    protected $custom_filter = ["behavior" => "getBehavior"];

    protected $filters = ["title"];



    public function __construct()
    {
        $this->resource = BehaviorsResource::class;
        //set messages
        $this->error = __("api.There is no Behaviors");
        $this->success = __('api.All Behaviors retrieved successfully');
        $this->saved = __('api.Behaviors created successfully');

        parent::__construct(Behaviors::query());
    }

    public function getBehavior($data)
    {
        if (isset(request("filter")['behavior']) && request("filter")['behavior'] != null) {
            if (request("filter")['behavior'] === 'good') {
                return $data->where('points', '>', 0);
            }

            return $data->where('points', '<', 0);
        }

        return $data;
    }

    public function GetAll()
    {
        return $this->Get(["behaviors.id", "title", "points","user_id"], ['table' => 'behaviors_data', 'key' => 'behavior_id']);
    }

    public function storeData()
    {
        $this->store(["points"], ["title"], "behavior_id");
        return $this->saved;
    }

    public function showData(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Behaviors::whereHas('Data')->where('id', $id)->first();
            if ($data != null) {
                $this->data = BehaviorsResourceEdit::make($data);
            }
        }
        return $this->success;
    }

    public function edit(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Behaviors::where('id', $id)->whereHas('Data')->first();
            if ($data != null) {
                $this->data = BehaviorsResourceEdit::make($data);
            }
        }
        return $this->success;
    }

    public function updateData(int $id)
    {
        $be = Behaviors::where('id', $id)->first();
        if ($be->user_id && $be->school_id == Utility::school_id()) {
            $this->update(["points"], ["title"], "behavior_id", $id);
            return $this->saved;
        }

        return __('api.You can not update this behavior');
    }

    public function deleteData(int $id)
    {
        $be = Behaviors::where('id', $id)->first();
        if ($be->user_id) {
            $this->delete($id);
        } else {
            return __('api.You can not update this behavior');
        }
    }
}
