<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsFocusController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/news', [NewsController::class, 'list']);

Route::middleware(['api'])->group(function() {
    Route::put('/user/login', [UserController::class, 'login']); // 保持 login 不變，因為前端 Login.vue 未提供
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::post('/user/register', [UserController::class, 'register']);

    Route::get('/news-focus', [NewsFocusController::class, 'list']);
    Route::post('/news-focus', [NewsFocusController::class, 'add']);
    Route::delete('/news-focus/{id}', [NewsFocusController::class, 'remove']);
});