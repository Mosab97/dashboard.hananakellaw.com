<?php

use App\Http\Controllers\API\Home\HomeController;
use App\Http\Controllers\API\Product\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->middleware('throttle:4,1')->group(function () {});

Route::prefix('v1')->middleware(['localization'])->group(function () {
    Route::get('/home', [HomeController::class, 'home']);
    Route::get('/appointment-types', [HomeController::class, 'appointmentTypes']);
    Route::post('/book-appointment', [HomeController::class, 'bookAppointment']);
    Route::get('/articles/{article}', [HomeController::class, 'article']);

});
