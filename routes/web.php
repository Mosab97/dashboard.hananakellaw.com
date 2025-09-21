<?php

use App\Http\Controllers\API\FcmTokenController;
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

// Route::get('/', 'App\Http\Controllers\MainController@index');
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/privacy_policy', function () {
    return view('privacy_policy');
});

// Route to test sending a notification to a token
Route::post('/test-notification', [FcmTokenController::class, 'testNotification']);

// Route to generate a real FCM token
Route::get('/generate-real-fcm-token', [FcmTokenController::class, 'realTokenGenerator']);
