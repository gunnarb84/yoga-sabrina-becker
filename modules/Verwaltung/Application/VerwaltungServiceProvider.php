<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application;

use Illuminate\Support\ServiceProvider;
use Yoga\Modules\Verwaltung\Application\NumberSequence\VerwaltungNumberSequenceResolver;
use Yoga\Platform\NumberSequence\Application\NumberSequenceResolver;

final class VerwaltungServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NumberSequenceResolver::class, VerwaltungNumberSequenceResolver::class);
    }
}
