<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/school/manager')->group(function () {

    Route::get('/get/country', [AuthController::class, 'getCountry']);

    Route::get('/packages', [AuthController::class, 'packages']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forget/password', [AuthController::class, 'forgetPassword']);
    Route::post('/reset/password', [AuthController::class, 'resetPassword']);

    // list of packages

    Route::middleware(['auth:sanctum', 'abilities:manager'])->group(function () {
        Route::post('/verify/otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend/otp', [AuthController::class, 'reSendOtp']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/profile/password', [AuthController::class, 'updatePassword']);
        Route::post('/profile/update', [AuthController::class, 'updateProfile']);

        Route::middleware('verified.phone')->group(function () {
            // list of grades
            Route::get('/grades', [AuthController::class, 'grades']);
            // list of countries
            Route::get('/countries', [AuthController::class, 'countries']);
            // create school
            Route::post('/school', [AuthController::class, 'addSchoolsData']);
            // update school
            Route::put('/school', [AuthController::class, 'updateSchoolsData'])->middleware('verified.package');

            // asign package to school
            Route::post('/assign/package', [AuthController::class, 'assignPackage']);
            Route::get('/check/payment', [AuthController::class, 'checkPayment']);
            Route::post('/check/coupon', [AuthController::class, 'checkCoupon']);
            // show my school
            Route::get('/school', [AuthController::class, 'MySchool'])->middleware('verified.package');
            // list rows
            Route::get('/rows', [AuthController::class, 'rows']);
            // list schools Rows
            Route::get('/school/rows', [AuthController::class, 'schoolRows']);
            // list school Grades
            Route::get('/school/grades', [AuthController::class, 'schoolGrades']);
            // list classes
            Route::get('/classes', [AuthController::class, 'classes']);
            // edit school store data
            Route::get('/store/edit', [AuthController::class, 'editStoreData'])->middleware('verified.package');
            // update school store data
            Route::put('/store/update', [AuthController::class, 'updateStoreData'])->middleware('verified.package');
        });
    });
});

// teacher routes
Route::prefix('/school/teacher')->middleware(['auth:sanctum', 'abilities:teacher'])->group(function () {

    Route::get('/rows', [AuthController::class, 'rows']);
    // list school Grades
    Route::get('/grades', [AuthController::class, 'schoolGrades']);
    // list classes
    Route::get('/classes', [AuthController::class, 'classes']);
});


Route::get('common/packages', [AuthController::class, 'packages']);



