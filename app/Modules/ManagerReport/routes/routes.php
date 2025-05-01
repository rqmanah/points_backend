<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ManagerReport\Controllers\adminController;

Route::middleware(['auth:sanctum','abilities:manager' , 'verified.package'])->prefix('school/manager/')->group(function () {
    Route::get('report', [adminController::class , 'report']);

});
