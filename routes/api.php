<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

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

Route::post('/uploadfile', [MediaController::class, 'uploadfile']);
Route::get('/media', [MediaController::class, 'allMedia']);
Route::get('/singlemedia/{filename}', [MediaController::class, 'getMedia']);
Route::delete('/deletemedia/{filename}', [MediaController::class, 'deleteMedia']);
Route::delete('/deleteselecmedia', [MediaController::class, 'deleteSelectedMedia']);


