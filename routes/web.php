<?php

use App\Http\Controllers\Line\AddLine\AddLineController;
use App\Http\Controllers\Line\SelectUserSent\SelectUserSentController;
use App\Http\Controllers\Setting\SettingController;
use Illuminate\Support\Facades\Artisan;
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
    Route::delete('user/delete/{id}', [App\Http\Controllers\HomeController::class, 'delete_post']);
    Route::post('user/get_api/get_message/{id}', [App\Http\Controllers\HomeController::class, 'get_message']);

    //setting
    Route::get('user/setting/home', [App\Http\Controllers\Setting\SettingController::class, 'index'])->name('user.setting.home');
    Route::post('user/configs/store', [SettingController::class, 'store'])->name('user.configs.store');
    Route::post('user/configs/update_status', [SettingController::class, 'update_status'])->name('user.configs.update_status');
    Route::post('update_status_otp/{id}', [SettingController::class, 'update_status_otp']);
    Route::get('user/get_api/setting', [App\Http\Controllers\Setting\SettingController::class, 'get_all_post'])->name('user.get_api.setting');
    
    //เพิ่มรายชื่อล็อกอิน
    Route::post('user/configs/store_line_login', [SettingController::class, 'store_line_login'])->name('user.configs.store_line_login');
    Route::post('user/get_api/get_addline_login/{id}', [SettingController::class, 'get_addline_login']);
    Route::delete('user/configs/delete/{id}', [SettingController::class, 'delete_post']);
    Route::post('user/select_user_login/store', [SettingController::class, 'select_user_login'])->name('user.select_user_login.store');


    //addline
    Route::get('user/addline/home', [AddLineController::class, 'index'])->name('user.addline.home');
    Route::post('user/addline/store', [AddLineController::class, 'store'])->name('user.addline.store');
    Route::post('user/get_api/get_addline/{id}', [AddLineController::class, 'get_addline']);
    Route::delete('user/addline/delete/{id}', [AddLineController::class, 'delete_post']);
    //import csv
    Route::post('file-import', [AddLineController::class, 'fileImport'])->name('file-import');
    
    //select_user_sent
    Route::get('user/select_user_sent/home', [SelectUserSentController::class, 'index'])->name('user.select_user_sent.home');
    Route::post('user/select_user_sent/store', [SelectUserSentController::class, 'store'])->name('user.select_user_sent.store');
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});