<?php

namespace App\Modules\StudentsBehaviors\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\StudentsBehaviors\Models\StudentsBehaviors;
use App\Modules\StudentsBehaviors\Resources\StudentsBehaviorsResource;

class adminController extends Controller
{

    public function index()
    {
        $behaviors = StudentsBehaviors::query();
        if (isset(request("filter")['behavior']) && request("filter")['behavior'] != null) {
            if (request("filter")['behavior'] === 'good') {
                $behaviors->where('points', '>', '0')->whereNull('prize_id');
            } else {
                $behaviors->where('points', '<', '0')->whereNull('prize_id');
            }
        }

        if(request('term') && request('term') != null){
            $behaviors->whereHas('user', function($query){
                $query->where('name', 'like', '%'.request('term').'%');
            });
        }
        $behaviors = $behaviors->get();

        return $this->sendResponse(StudentsBehaviorsResource::collection($behaviors), __('Student Behaviors List'));
    }
}
