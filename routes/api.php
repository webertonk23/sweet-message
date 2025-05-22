<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, "register"]);
    
    Route::post('/login', [AuthController::class, "login"]);
    
    Route::get('/google/redirect', [AuthController::class, 'googleRedirect']);
    Route::get('/google/callback', [AuthController::class, 'googleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('messages', MessageController::class);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Public routes
Route::get('/{message}', [MessageController::class, 'showPublic']);
