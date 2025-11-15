<?php

use App\Http\Controllers\EpisodeController;
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


Route::middleware('auth:sanctum')->group(function(){

Route::post('/logout',[UserController::class,'logout']);

//Podcasts
Route::get('/podcasts',[PodcastController::class,'index']);
Route::get('/podcasts/{podcast}',[PodcastController::class,'show']);
Route::post('/podcasts',[PodcastController::class,'store']);
Route::put('/podcasts/{podcast}', [PodcastController::class,'update']);
Route::delete('/podcasts/{podcast}',[PodcastController::class,'destroy']);

//Episodes
Route::get('/podcasts/{podcast}/episodes', [EpisodeController::class,'index']);
Route::get('/episodes/{episode}', [EpisodeController::class,'show']);
Route::post('/podcasts/{podcast_id}/episodes', [EpisodeController::class,'store']);
Route::put('/episodes/{id}', [EpisodeController::class,'update']);
Route::delete('/episodes/{id}', [EpisodeController::class,'destroy']);

//Animateur
Route::post('/hosts',[UserController::class,'register']);
Route::put('/hosts/{id}',[UserController::class,'update']);
Route::delete('/hosts/{id}',[UserController::class,'destory']);
Route::get('/hosts',[UserController::class,'index']);
Route::get('/hosts/{id}',[UserController::class,'show']);

});





