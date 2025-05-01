<?php

use App\Modules\StudentPrizeOrder\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:student'])->prefix('school/student/')->group(function () {
    Route::get('prizes', [adminController::class, 'prizes']);
    Route::post('prize/order', [adminController::class, 'createOrder']);
    Route::post('prize/cancel/{id}', [adminController::class, 'cancelOrder']);
    Route::get('orders', [adminController::class, 'listMyOrders']);
    // schoolStoreData
    Route::get('store/data', [adminController::class, 'schoolStoreData']);
});
