<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\User\Auth\LoginController;
use App\Http\Controllers\API\v1\User\Auth\RegisterController;

Route::prefix('/v1')->group(function () {
    Route::controller(RegisterController::class)->group(function () {
        Route::post('/register',  'register');
        Route::post('/register/verify',  'verifyRegistration');
    });

    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
    });
});
