<?php

namespace App\Modules\Behaviors\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Modules\Behaviors\Models\Behaviors;
use App\Http\Controllers\AdminBaseController;
use App\Modules\Behaviors\Requests\StoreRequest;
use App\Modules\Behaviors\Requests\UpdateRequest;
use App\Modules\Behaviors\Models\FavouriteBehaviors;
use App\Modules\Behaviors\Services\BehaviorsService;
use App\Modules\Behaviors\Resources\BehaviorsResource;

class adminController extends AdminBaseController
{
    public function __construct()
    {
        $this->service = new BehaviorsService();
        $this->StoreRequest = new StoreRequest();
        $this->UpdateRequest = new UpdateRequest();
    }

    // is_favourite toggle
    public function favouriteToggle($id)
    {

        try {
            // Check if the behaviour is already marked as favourite
            $favourite = FavouriteBehaviors::where('behavior_id', $id)->first();

            if (!$favourite) {
                // If not, create a new favourite
                $favourite = new FavouriteBehaviors();
                $favourite->behavior_id = $id;
                $favourite->save();

                return $this->sendResponse(['id' => $favourite->id], 'Favourite added successfully');
            }
            // If found, delete the existing favourite
            $favourite->delete();

            return $this->sendResponse([], 'Favourite removed successfully');
        } catch (\Exception $e) {
            // Handle any errors (e.g., DB issues)
            return $this->sendResponse([], 'An error occurred: ' . $e->getMessage(), 500);
        }
    }

    // get all behaviors
    public function  getFavouriteBehaviors()
    {
        $favouriteBehaviors = FavouriteBehaviors::where('user_id', Auth::guard('sanctum')?->user()?->id)->pluck('behavior_id');
        $behaviours = Behaviors::whereIn('id', $favouriteBehaviors)->get();
        return $this->sendResponse(BehaviorsResource::collection($behaviours), 'behaviours retrieved successfully');
    }
}
