<?php

namespace App\Providers;

use App\Modules\Report\Contracts\DataSourceInterface;
use App\Modules\Report\DataSources\ApiDataSource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DataSourceInterface::class, ApiDataSource::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
