<?php

use App\Modules\Settings\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;


Route::get('school/settings', [SettingsController::class, 'index']);




