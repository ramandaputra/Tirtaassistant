<?php

use Illuminate\Support\Facades\Route;

Route::get('/knowledge-base', function () {
    return view('knowledge-base');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chatbot', function () {
    return view('welcome');
});

// Serve widget.js with CORS + caching headers for cross-origin embedding
Route::get('/widget.js', function () {
    $path = public_path('widget.js');

    return response()->file($path, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control' => 'public, max-age=86400',
    ]);
});
