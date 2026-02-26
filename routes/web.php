<?php

use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/docs'));

Route::get('/docs', [SwaggerController::class, 'ui']);
Route::get('/docs/json', [SwaggerController::class, 'json']);
