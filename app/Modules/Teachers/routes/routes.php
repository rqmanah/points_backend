<?php

use App\Modules\Teachers\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->group(function () {
    Route::post('school/manager/teachers/destroy', [adminController::class, 'deleteTeachers']);
    Route::get('school/manager/teachers/export', [adminController::class, 'downloadExcel']);
    Route::resource('school/manager/teachers', adminController::class)->except(['destroy']);
    Route::post('school/manager/teachers/storeExcel', [adminController::class, 'storeExcel']);
    // update password
    Route::post('school/manager/teachers/update-password', [adminController::class, 'updatePassword']);

});
