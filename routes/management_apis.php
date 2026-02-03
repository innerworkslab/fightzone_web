<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\Management\LoginController;
use App\Http\Controllers\API\v1\Management\AdminController;
use App\Http\Controllers\API\v1\Management\UserController;
use App\Http\Controllers\API\v1\Management\PaymentMethodController;
use App\Http\Controllers\API\v1\Management\PackageController;
use App\Http\Controllers\API\v1\Management\DepositController;
use App\Http\Controllers\API\v1\Management\PurchaseController;
use App\Http\Controllers\API\v1\Management\CourseCategoryController;
use App\Http\Controllers\API\v1\Management\CourseController;
use App\Http\Controllers\API\v1\Management\CourseLevelController;

Route::prefix('/v1/management')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth:management_api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);

        Route::middleware(['is.active'])->group(function () {
            Route::prefix('/admins')->group(function () {
                Route::controller(AdminController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });

            Route::prefix('/users')->group(function () {
                Route::controller(UserController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });

            Route::prefix('/payment-methods')->group(function () {
                Route::controller(PaymentMethodController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });

            Route::prefix('/packages')->group(function () {
                Route::controller(PackageController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });

            Route::prefix('/deposits')->group(function () {
                Route::get('/', [DepositController::class, 'index']);
                Route::get('/{id}', [DepositController::class, 'show']);
                Route::post('/{id}/confirm', [DepositController::class, 'confirm']);
                Route::post('/{id}/reject', [DepositController::class, 'reject']);
            });

            Route::prefix('/purchases')->group(function () {
                Route::get('/', [PurchaseController::class, 'index']);
                Route::get('/{id}', [PurchaseController::class, 'show']);
                Route::post('/{id}/confirm', [PurchaseController::class, 'confirm']);
                Route::post('/{id}/reject', [PurchaseController::class, 'reject']);
            });

            Route::prefix('/course-categories')->group(function () {
                Route::controller(CourseCategoryController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });

            Route::prefix('/courses')->group(function () {
                Route::controller(CourseController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });

            Route::prefix('/course-levels')->group(function () {
                Route::controller(CourseLevelController::class)->group(function () {
                    Route::get('/', 'index');
                    Route::get('/{id}', 'show');
                    Route::post('/', 'store');
                    Route::post('/{id}', 'update');
                    Route::post('/{id}/toggle', 'toggle');
                    Route::delete('/{id}', 'destroy');
                });
            });
        });
    });
});
