<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class SlugWithDotProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Str::macro('slugWithDot', function ($value) {

            $value = str_replace('.', 'DOTPLACEHOLDER', $value);

            $slug = Str::slug($value);

            return str_replace('dotplaceholder', '.', $slug);
        });
    }
}
