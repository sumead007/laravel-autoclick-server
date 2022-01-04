<?php

use App\Http\Controllers\LineLoginOTPController;
use App\Http\Controllers\LineNotifyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', function () {
    $credentials = request()->only(['email', 'password']);
    if (!auth()->validate($credentials)) {
        abort(401);
    } else {
        $user = User::where('email', $credentials['email'])->first();
        $user->tokens()->delete();
        $token = $user->createToken('postman');
        return response()->json(['token' => $token->plainTextToken]);
    }
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('addline', 'App\Http\Controllers\AddlineController');
    Route::resource('message', 'App\Http\Controllers\MessageController');
    Route::resource('config', 'App\Http\Controllers\ConfigController');
    Route::resource('line_login', 'App\Http\Controllers\LineLoginController');
    Route::post('line_notify', [LineNotifyController::class, 'sent_message']);
    Route::post('update_status_otp/{id}', [LineLoginOTPController::class, 'update_status_otp']);
    Route::resource('line_login_otp', 'App\Http\Controllers\LineLoginResourceController');

});
