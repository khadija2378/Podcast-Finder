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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);
Route::post('/reset',[UserController::class,'resetPassword']);


Route::middleware('auth:sanctum')->group(function(){

Route::post('/logout',[UserController::class,'logout']);

//Podcasts
Route::get('/podcasts',[PodcastController::class,'index']);
Route::get('/podcasts/{podcast}',[PodcastController::class,'show']);
Route::post('/podcasts',[PodcastController::class,'store'])->middleware('role:animateur,admin');
Route::put('/podcasts/{podcast}', [PodcastController::class,'update'])->middleware('role:animateur,admin');
Route::delete('/podcasts/{podcast}',[PodcastController::class,'destroy'])->middleware('role:animateur,admin');

//Episodes
Route::get('/podcasts/{podcast}/episodes', [EpisodeController::class,'index']);
Route::get('/episodes/{episode}', [EpisodeController::class,'show']);
Route::post('/podcasts/{podcast}/episodes', [EpisodeController::class,'store'])->middleware('role:animateur,admin');
Route::put('/episodes/{episode}', [EpisodeController::class,'update'])->middleware('role:animateur,admin');
Route::delete('/episodes/{episode}', [EpisodeController::class,'destroy'])->middleware('role:animateur,admin');

//Animateur
Route::get('/hosts',[UserController::class,'hosts']);
Route::get('/hosts/{host}',[UserController::class,'show']);


});
//Admin
Route::middleware('auth:sanctum','role:admin')->group(function(){

Route::get('/users',[UserController::class,'index']);
Route::post('/users',[UserController::class,'register']);
Route::put('/users/{user}',[UserController::class,'update']);
Route::delete('/users/{user}',[UserController::class,'destroy']);

});





