<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Persistence;

use Illuminate\Support\ServiceProvider;

final class WebseiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
