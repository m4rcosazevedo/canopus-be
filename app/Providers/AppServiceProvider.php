<?php

namespace App\Providers;

use App\Modules\Report\Contracts\DataSourceInterface;
use App\Modules\Report\Contracts\ReportStorageInterface;
use App\Modules\Report\DataSources\ApiDataSource;
use App\Modules\Report\Services\ReportStorageManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            DataSourceInterface::class,
            ApiDataSource::class
        );

        $this->app->bind(
            ReportStorageInterface::class,
            ReportStorageManager::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
