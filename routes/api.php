<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('ai')->group(function () {
    Route::get('/start', [App\Http\Controllers\Api\AiAvatarController::class, 'start']);
    Route::post('/process', [App\Http\Controllers\Api\AiAvatarController::class, 'process']);
});
