<?php

use App\Http\Controllers\PodcastController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);
Route::post('/reset',[UserController::class,'resetPassword']);
Route::post('/logout',[UserController::class,'logout'])->middleware('auth:sanctum');

Route::get('/podcasts',[PodcastController::class,'index'])->middleware('auth:sanctum');
Route::get('/podcasts/{podcast}',[PodcastController::class,'show'])->middleware('auth:sanctum');
Route::post('/podcasts',[PodcastController::class,'store'])->middleware('auth:sanctum');
Route::post('/podcasts/{podcast}', [PodcastController::class,'update'])->middleware('auth:sanctum');
Route::delete('/podcasts/{podcast}',[PodcastController::class,'destroy'])->middleware('auth:sanctum');

Route::resource('/episodes', PodcastController::class)->middleware('auth:sanctum');
