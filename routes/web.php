<?php

use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/reset-password', function () {
    $user           = Users::where('user_name', 'hsan9890')->first();
    if (!$user) {
        return 'User not found';
    }
    $user->password = Hash::make('hsan9890');
    $user->save();
    return 'Password reset successfully';
});
