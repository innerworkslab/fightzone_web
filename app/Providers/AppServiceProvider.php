<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Relation::enforceMorphMap([
            'admin' => 'App\Models\Admin',
            'user' => 'App\Models\User',
            'deposit' => 'App\Models\Deposit',
            'package' => 'App\Models\Package',
            'course' => 'App\Models\Course'
        ]);
    }
}
