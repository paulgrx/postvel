<?php

use App\Http\Controllers\ClearController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostfixController;
use App\Http\Controllers\RecipientController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware([EnsureTokenIsValid::class])
    ->group(function() {
        Route::post('/messages', [MessageController::class, 'store']);
        Route::get('/messages/{message}/recipients', [RecipientController::class, 'index']);
        Route::get('/messages/{message}/progress', [RecipientController::class, 'progress']);
        Route::post('/messages/{message}/recipients', [RecipientController::class, 'store']);

        Route::post('/postfix', [PostfixController::class, 'store']);

        Route::post('/clear', [ClearController::class, 'store']);
    });
