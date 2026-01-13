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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
