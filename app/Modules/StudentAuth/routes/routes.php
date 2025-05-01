<?php

use App\Modules\StudentAuth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/school/student')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'abilities:student'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        // update student
        Route::post('/update', [AuthController::class, 'update']);
        // update password
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
    });
});
