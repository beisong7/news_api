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
    return response()->json(['project'=>'innoscripta assessment', 'health'=>'looks good','version'=>'0']);
});

Route::get('/require-login', function(){
    return response()->json(['success'=>'failed','message'=>'auth attempt failure or missing header values']);
})->name('require-login');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
