<?php

use App\Http\Controllers\Setting\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('user.setting.home');
    } else {
        return view('auth.login');
    }
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::post('user/save_change', [App\Http\Controllers\HomeController::class, 'save_change'])->name('user.save_change');
    Route::post('user/store', [App\Http\Controllers\HomeController::class, 'store'])->name('user.store');

    //setting
    Route::get('user/setting/home', [App\Http\Controllers\Setting\SettingController::class, 'index'])->name('user.setting.home');
    Route::post('user/configs/store', [SettingController::class, 'store'])->name('user.configs.store');
    Route::post('user/configs/update_status', [SettingController::class, 'update_status'])->name('user.configs.update_status');

});
