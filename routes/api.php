<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('ai')->group(function () {
    Route::post('/start-session', [App\Http\Controllers\Api\AiAvatarController::class, 'startSession']);
    Route::post('/send-message', [App\Http\Controllers\Api\AiAvatarController::class, 'sendMessage']);
    Route::post('/reset-session', [App\Http\Controllers\Api\AiAvatarController::class, 'resetSession']);
});
