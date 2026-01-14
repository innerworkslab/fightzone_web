<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\User\Auth\LoginController;
use App\Http\Controllers\API\v1\User\Auth\RegisterController;

use App\Http\Controllers\API\v1\User\Shop\PaymentMethodController;
use App\Http\Controllers\API\v1\User\Shop\PackageController;
use App\Http\Controllers\API\v1\User\Shop\DepositController;
use App\Http\Controllers\API\v1\User\Shop\PurchaseController;
use App\Http\Controllers\API\v1\User\ProfileController;

Route::prefix('/v1')->group(function () {
    Route::controller(RegisterController::class)->group(function () {
        Route::post('/register',  'register');
        Route::post('/register/verify',  'verifyRegistration');
    });

    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);

        Route::middleware(['is.verified', 'is.active'])->group(function () {
            Route::prefix('/profile')->group(function () {
                Route::get('/', [ProfileController::class, 'index']);
                Route::get('/details', [ProfileController::class, 'show']);
                Route::get('/balance', [ProfileController::class, 'balance']);
                Route::get('/deposits', [ProfileController::class, 'deposits']);
                Route::get('/purchases', [ProfileController::class, 'purchases']);
            });

            Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
            Route::get('/packages', [PackageController::class, 'index']);

            Route::prefix('/deposits')->group(function () {
                Route::get('/', [DepositController::class, 'index']);
                Route::post('/', [DepositController::class, 'store']);
            });

            Route::prefix('/purchases')->group(function () {
                Route::get('/', [PurchaseController::class, 'index']);
                Route::post('/', [PurchaseController::class, 'store']);
                Route::get('/{id}', [PurchaseController::class, 'show']);
            });
        });
    });
});
