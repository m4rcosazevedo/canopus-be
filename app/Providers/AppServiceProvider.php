<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Report\Contracts\DataSourceInterface::class,
            \App\Modules\Report\DataSources\ApiDataSource::class
        );

        $this->app->bind(
            \App\Modules\Report\Contracts\ReportStorageInterface::class,
            \App\Modules\Report\Services\ReportStorageManager::class
        );

        $this->app->register(SlugWithDotProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Modules\UserType\Model\UserType::observe(\App\Modules\UserType\Observers\UserTypeObserver::class);
    }
}
