<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\Management\LoginController;
use App\Http\Controllers\API\v1\Management\AdminController;
use App\Http\Controllers\API\v1\Management\UserController;
use App\Http\Controllers\API\v1\Management\PaymentMethodController;
use App\Http\Controllers\API\v1\Management\DepositController;

Route::prefix('/v1/management')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth:management_api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);

        Route::prefix('/admins')->group(function(){
            Route::controller(AdminController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::post('/{id}', 'update');
                Route::post('/{id}/toggle', 'toggle');
                Route::delete('/{id}', 'destroy');
            });
        });

        Route::prefix('/users')->group(function(){
            Route::controller(UserController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::post('/{id}', 'update');
                Route::post('/{id}/toggle', 'toggle');
                Route::delete('/{id}', 'destroy');
            });
        });

        Route::prefix('/payment-methods')->group(function(){
            Route::controller(PaymentMethodController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::post('/{id}', 'update');
                Route::post('/{id}/toggle', 'toggle');
                Route::delete('/{id}', 'destroy');
            });
        });

        Route::prefix('/deposits')->group(function(){
            Route::get('/', [DepositController::class, 'index']);
            Route::get('/{id}', [DepositController::class, 'detail']);
            Route::post('/{id}/confirm', [DepositController::class, 'confirm']);
            Route::post('/{id}/reject', [DepositController::class, 'reject']);
        });
    });
});
