<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::post('/chatbot', [ChatbotController::class, 'reply']);

// Widget API (embeddable chatbot)
Route::prefix('widget')->middleware('widget.cors')->group(function () {
    Route::post('/session', [WidgetController::class, 'startSession']);
    Route::post('/message', [WidgetController::class, 'sendMessage']);

    // Handle CORS preflight for browsers
    Route::options('/session', fn () => response('', 204));
    Route::options('/message', fn () => response('', 204));
});
