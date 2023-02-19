<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;

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

Route::get('/ping', function(){
    return ['pong' => true];
});

Route::get('/401',[AuthController::class, 'unAuthorized'])->name('login');

Route::post('/auth/login', [AuthController::class,'login']);
Route::post('/auth/logout', [AuthController::class,'logout']);
Route::post('/auth/refresh', [AuthController::class,'refresh']);

Route::post('/user', [UserController::class,'create']);
Route::put('/user', [UserController::class,'update']);
Route::get('/user/{id?}', [UserController::class,'read']);
Route::post('/user/avatar', [UserController::class,'updateAvatar']);
Route::post('/user/cover', [UserController::class,'updateCover']);
Route::post('/user/follow/{id}', [UserController::class,'follow']);
Route::get('/user/followers/{id}', [UserController::class,'followers']);
Route::get('/user/photos/{id}', [UserController::class,'photos']);

Route::get('/feed', [FeedController::class,'read']);
Route::post('/feed', [FeedController::class,'create']);
Route::get('/feed/user/{id?}', [FeedController::class,'userFeed']);

Route::post('/post/like/{id}', [PostController::class,'like']);
Route::post('/post/comment/{id}', [PostController::class,'comment']);

Route::get('/search', [SearchController::class,'search']);

