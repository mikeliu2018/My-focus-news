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


Route::Get('/news/list', [NewsController::class, 'list']);

Route::middleware(['api'])->group(function() {
    Route::Put('/user/login', [UserController::class, 'login']);
    Route::Put('/user/logout', [UserController::class, 'logout']);
    Route::post('/user/register', [UserController::class, 'register']);

    Route::get('/news_focus/list', [NewsFocusController::class, 'list']);
    Route::post('/news_focus/add', [NewsFocusController::class, 'add']);    
    Route::delete('/news_focus/remove', [NewsFocusController::class, 'remove']);
});