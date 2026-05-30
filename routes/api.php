<?php

use App\Http\Controllers\Api\ShortenController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/shorten', [ShortenController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('api.shorten');
