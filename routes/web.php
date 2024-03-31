<?php

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
    return view('index');
});


Route::post('login',[\App\Http\Controllers\AuthController::class,'login']);
Route::post('validate_auth',function (){
    return \Illuminate\Support\Facades\Auth::check();
});

Route::post('register',[\App\Http\Controllers\AuthController::class,'register']);

// for vue ⬇️
Route::prefix('app')->group(function () {
    Route::get('{any?}', function () {
        return view('index');
    })->where('any', '.*');
});

Route::middleware(['auth'])->group(function () {

    Route::post('logout',[\App\Http\Controllers\AuthController::class,'logout']);
    Route::resource('feedback',\App\Http\Controllers\FeedbackController::class);
    Route::resource('comments-manager',\App\Http\Controllers\FeedbackCommentsManager::class);
    Route::get('get-mentions',[\App\Http\Controllers\AuthController::class,'get_mentions']);

});


Route::redirect('/', '/app');
