<?php

namespace App\Modules\Subscriptions\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Modules\Subscriptions\Services\SubscriptionsService;


class adminController extends AdminBaseController
{
    public function __construct()
    {
        $this->service = new SubscriptionsService();
    }

    //  available permissions
    public function availablePermissions()
    {
        $data = $this->service?->availablePermissions();
        return $this->sendResponse($data, __('api.Permissions retrieved successfully'));
    }
}
