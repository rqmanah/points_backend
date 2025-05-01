<?php

use App\Modules\TeachersAuth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/school/teacher')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'abilities:teacher'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        // update teacher
        Route::post('/update', [AuthController::class, 'update']);
        // update password
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
    });
});
