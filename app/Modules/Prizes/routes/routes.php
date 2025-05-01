<?php

use App\Modules\Prizes\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->group(function () {

    Route::resource('school/manager/prizes', adminController::class);
    Route::get('school/manager/prize/stock', [adminController::class, 'getStock']);

});
