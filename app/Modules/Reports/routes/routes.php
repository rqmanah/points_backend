<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Reports\Controllers\adminController;
use App\Modules\Reports\Controllers\ExportPdfController;

Route::middleware(['auth:sanctum', 'abilities:manager', 'verified.package'])->group(function () {
    Route::get('school/manager/most-active-teachers', [adminController::class, 'mostActiveTeachers']);
    Route::get('school/manager/most-active-students', [adminController::class, 'mostActiveStudents']);
    Route::get('school/manager/teacher-behavior-report', [adminController::class, 'teacherBehaviorReport']);
    Route::get('school/manager/behavior-report', [adminController::class, 'behaviorReport']);
    Route::get('school/manager/top-students', [adminController::class, 'topStudentsByPoints']);
    Route::get('school/manager/class_report', [adminController::class, 'classRowPointsReport']);
    Route::get('school/manager/delay-report', [adminController::class, 'classRowPointsDelayReport']);

    Route::get('school/manager/teacher-behavior-report-pdf', [ExportPdfController::class, 'exportTeacherBehaviorReportToPDF']);

});
