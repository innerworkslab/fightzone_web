<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\Management\LoginController;
use App\Http\Controllers\API\v1\Management\AdminController;

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
    });
});
