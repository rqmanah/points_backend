<?php

use App\Modules\Tickets\Controllers\adminController;
use App\Modules\Tickets\Controllers\commentAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->group(function () {
    Route::resource('school/manager/tickets', adminController::class)->only(['show', 'store', 'index']);

    Route::resource('school/manager/tickets/comment', commentAdminController::class)->only(['store']);
    // close ticket
    Route::post('school/manager/tickets/{id}/close', [adminController::class, 'closeTicket']);
});
