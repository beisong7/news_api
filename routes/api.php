<?php

use App\Http\Controllers\API\NewsArticleController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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


// Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::get('gen-sync-key',[SyncController::class,'syncKey']);

Route::get('news', [NewsArticleController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    // User routes
    Route::post('logout',[UserAuthController::class,'logout']);

    Route::get('profile', [UserController::class, 'profile']);

    Route::prefix('news')->group(function () {

        Route::get('/search', [NewsArticleController::class, 'search']);
        Route::get('/recommended', [NewsArticleController::class, 'getRecommended'])->middleware('auth:api');
        Route::get('/{news}', [NewsArticleController::class, 'show']);
    });
});

Route::middleware('sync.news')->group(function () {
    // Sunc routes
    Route::post('sync/news',[SyncController::class,'syncNews']);
});
