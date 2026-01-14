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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
