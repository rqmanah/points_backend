<?php

namespace App\Modules\Settings\Controllers;

use App\Modules\Settings\Models\Settings;
use App\Modules\Settings\Resources\SettingsResource;
use App\Http\Controllers\Controller;


class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::first();
        return $this->sendResponse(SettingsResource::make($settings), 'Settings retrieved successfully');
    }

}
