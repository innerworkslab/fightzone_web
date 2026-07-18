<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

use App\Services\ThirdParty\Video\Providers\VimeoVideoProvider;
use App\Services\ThirdParty\Video\Providers\YoutubeVideoProvider;
use App\Services\ThirdParty\Video\VideoMetadataService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(VideoMetadataService::class, function () {
            return new VideoMetadataService([
                new YoutubeVideoProvider(),
                new VimeoVideoProvider(),
            ]);
        });
    }

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
