<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('components.layouts.app');
});

Route::get('/knowledge-base', function () {
    return view('knowledge-base');
});
