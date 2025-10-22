<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// SSO Authentication endpoints
Route::post('/sso/authenticate', [SsoController::class, 'authenticate']);
Route::post('/sso/verify', [SsoController::class, 'verify'])->middleware('auth:sanctum');
