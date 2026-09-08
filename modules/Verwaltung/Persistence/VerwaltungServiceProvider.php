<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Persistence;

use Illuminate\Support\ServiceProvider;

final class VerwaltungServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
