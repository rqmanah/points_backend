<?php

use App\Modules\StudentsBehaviors\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum','abilities:student'])->prefix('school/student/')->group(function () {

    Route::resource('behavior', adminController::class);

});
