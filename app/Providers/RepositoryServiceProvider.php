<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Admin\AdminRepositoryInterface;
use App\Repositories\Admin\AdminRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;
use App\Repositories\PaymentMethod\PaymentMethodRepository;
use App\Repositories\Deposit\DepositRepositoryInterface;
use App\Repositories\Deposit\DepositRepository;
use App\Repositories\Package\PackageRepositoryInterface;
use App\Repositories\Package\PackageRepository;
use App\Repositories\Purchase\PurchaseRepositoryInterface;
use App\Repositories\Purchase\PurchaseRepository;
use App\Repositories\Profile\ProfileRepositoryInterface;
use App\Repositories\Profile\ProfileRepository;
use App\Repositories\CourseCategory\CourseCategoryRepositoryInterface;
use App\Repositories\CourseCategory\CourseCategoryRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Course\CourseRepository;
use App\Repositories\CourseDay\CourseDayRepositoryInterface;
use App\Repositories\CourseDay\CourseDayRepository;
use App\Repositories\CourseLevel\CourseLevelRepositoryInterface;
use App\Repositories\CourseLevel\CourseLevelRepository;
use App\Repositories\LessonDay\LessonDayRepositoryInterface;
use App\Repositories\LessonDay\LessonDayRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->bind(DepositRepositoryInterface::class, DepositRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(PurchaseRepositoryInterface::class, PurchaseRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(CourseCategoryRepositoryInterface::class, CourseCategoryRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(CourseDayRepositoryInterface::class, CourseDayRepository::class);
        $this->app->bind(CourseLevelRepositoryInterface::class, CourseLevelRepository::class);
        $this->app->bind(LessonDayRepositoryInterface::class, LessonDayRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
