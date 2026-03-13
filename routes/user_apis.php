<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\User\Auth\LoginController;
use App\Http\Controllers\API\v1\User\Auth\RegisterController;

use App\Http\Controllers\API\v1\User\Consumption\LessonDayController;
use App\Http\Controllers\API\v1\User\Consumption\WalkinController;

use App\Http\Controllers\API\v1\User\Profile\ProfileController;
use App\Http\Controllers\API\v1\User\Profile\NotificationController;

use App\Http\Controllers\API\v1\User\Purchase\DepositController;
use App\Http\Controllers\API\v1\User\Purchase\PaymentMethodController;
use App\Http\Controllers\API\v1\User\Purchase\PurchaseController;

use App\Http\Controllers\API\v1\User\Shop\CourseController;
use App\Http\Controllers\API\v1\User\Shop\CourseCategoryController;
use App\Http\Controllers\API\v1\User\Shop\PackageController;

use App\Http\Controllers\API\v1\User\Misc\FeaturedImageController;

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

            Route::get('/featured-images', [FeaturedImageController::class, 'index']);

            Route::prefix('/course-categories')->group(function () {
                Route::get('/', [CourseCategoryController::class, 'index']);
                Route::get('/{id}', [CourseCategoryController::class, 'show']);
            });

            Route::prefix('/courses')->group(function () {
                Route::get('/', [CourseController::class, 'index']);
                Route::get('/category/{categoryId}', [CourseController::class, 'getByCategory']);
                Route::get('/level/{level}', [CourseController::class, 'getByLevel']);
                Route::get('/{id}', [CourseController::class, 'show']);
                Route::get('/{id}/course_levels/{levelId}/lesson_days', [CourseController::class, 'getCourseLevelLessonDays']);
                Route::get('/{courseId}/course_levels/{levelId}/lesson_days/{id}', [LessonDayController::class, 'show']);
            });

            Route::prefix('/lesson_day_videos')->group(function () {
                Route::post('/{lessonDayVideoId}/complete', [LessonDayController::class, 'markVideoCompletion']);
                Route::get('/{lessonDayVideoId}/prev-next', [LessonDayController::class, 'prevNextVideo']);
            });

            Route::prefix('/deposits')->group(function () {
                Route::get('/', [DepositController::class, 'index']);
                Route::post('/', [DepositController::class, 'store']);
            });

            Route::prefix('/purchases')->group(function () {
                Route::get('/', [PurchaseController::class, 'index']);
                Route::post('/', [PurchaseController::class, 'store']);
                Route::get('/{id}', [PurchaseController::class, 'show']);
            });

            Route::prefix('/walk-ins')->group(function () {
                Route::get('/qr-payload', [WalkinController::class, 'qrPayload']);
            });

            Route::controller(NotificationController::class)->group(function(){
                Route::get('/notifications', 'index');
                Route::get('/notifications/unread_count', 'getUnreadCount');
                Route::post('/notifications/{id}', 'markAsRead');
            });
        });
    });
});
