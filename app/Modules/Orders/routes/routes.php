<?php

use App\Modules\Orders\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->group(function () {

    Route::resource('school/manager/orders', adminController::class)->only([
        'index', 'show', 'edit'
    ]);
    // cancel order
    Route::post('school/manager/orders/cancel/{id}', [adminController::class, 'cancel']);
    // complete order
    Route::post('school/manager/orders/complete/{id}', [adminController::class, 'complete']);

});
