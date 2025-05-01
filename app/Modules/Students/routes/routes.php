<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Students\Controllers\adminController;
use App\Modules\Students\Controllers\ExportPdfController;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->prefix('school/manager/')->group(function () {
    Route::post('students/destroy', [adminController::class, 'deleteStds']);
    Route::get('students/export', [adminController::class, 'downloadExcel']);
    Route::post('students/storeExcel', [adminController::class, 'storeExcel']);
    Route::post('students/addBehavior', [adminController::class, 'addBehavior']);
    Route::get('students/studentsIndex', [adminController::class, 'studentsIndex']);
    Route::get('students/behaviors/details/{id}', [adminController::class, 'studentBehaviorDetails']);
    Route::get('students/behaviors/top', [adminController::class, 'studentBehaviorTop']);
    Route::resource('students', adminController::class)->except(['destroy']);
    // update password
    Route::post('students/update-password', [adminController::class, 'updatePassword']);

    // export students

});
Route::middleware(['auth:sanctum', 'abilities:teacher'])->prefix('school/teacher/')->group(function () {
    Route::post('students/addBehavior', [adminController::class, 'addBehaviorTeacher']);
    Route::get('students/studentsIndex', [adminController::class, 'studentsIndex']);
    Route::get('students/behaviors/details/{id}', [adminController::class, 'studentBehaviorDetails']);
    Route::get('students/behaviors/top', [adminController::class, 'studentBehaviorTop']);
    Route::resource('students', adminController::class)->only(['show']);

    // export students
    Route::get('top/students/export', [ExportPdfController::class, 'exportStudentBehaviorTopToPDF']);
    Route::get('index/students/export', [ExportPdfController::class, 'exportStudentsIndexToPDF']);

});
