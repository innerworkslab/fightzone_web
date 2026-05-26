<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require base_path('routes/channels.php');

        Relation::enforceMorphMap([
            'admin' => 'App\Models\Admin',
            'user' => 'App\Models\User',
            'deposit' => 'App\Models\Deposit',
            'package' => 'App\Models\Package',
            'course' => 'App\Models\Course',
            'course_level' => 'App\Models\CourseLevel',
            'purchase' => 'App\Models\Purchase',
            'package_purchase' => 'App\Models\PackagePurchase',
            'course_level_purchase' => 'App\Models\CourseLevelPurchase',
        ]);
    }
}
