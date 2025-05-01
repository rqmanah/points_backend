<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StudentReport\Controllers\adminController;

Route::middleware(['auth:sanctum','abilities:student'])->prefix('school/student/')->group(function () {
    Route::get('report', [adminController::class , 'report']);

});
