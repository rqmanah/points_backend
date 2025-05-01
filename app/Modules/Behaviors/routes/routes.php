<?php

use App\Modules\Behaviors\Controllers\adminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->group(function () {
    Route::resource('school/manager/behaviors', adminController::class);
});

Route::middleware(['auth:sanctum', 'abilities:teacher'])->prefix('school/teacher/')->group(function () {
    Route::get('behaviors/behaviorsIndex', [adminController::class, 'index']);
    // behaviors is_favorite toggle
    Route::get('behaviors/favorite/toggle/{id}', [adminController::class, 'favouriteToggle']);
    // get all favorite behaviors
    Route::get('behaviors/get-favourite-behaviors', [adminController::class, 'getFavouriteBehaviors']);
});
