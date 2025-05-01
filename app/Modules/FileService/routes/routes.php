<?php

use App\Modules\FileService\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum' ])->group(function () {
    Route::post('school/store', [adminController::class, 'store']);
});
