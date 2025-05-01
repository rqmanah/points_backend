<?php

use App\Modules\TeacherTickets\Controllers\adminController;
use App\Modules\TeacherTickets\Controllers\commentAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:teacher' ])->group(function () {
    Route::resource('school/teacher/tickets', adminController::class)->only(['show','store','index']);
    Route::resource('school/teacher/tickets/comment', commentAdminController::class)->only(['store']);
});
