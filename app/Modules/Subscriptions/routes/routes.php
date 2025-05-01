<?php

use App\Modules\Subscriptions\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:manager'])->group(function () {
    Route::get('school/manager/subscriptions/permissions', [adminController::class, 'availablePermissions']);
    Route::resource('school/manager/subscriptions', adminController::class)->only(['index', 'show']);
});
