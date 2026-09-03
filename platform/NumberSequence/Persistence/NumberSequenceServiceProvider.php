<?php

declare(strict_types=1);

namespace Yoga\Platform\NumberSequence\Persistence;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Yoga\Platform\NumberSequence\Application\NextNumber;

final class NumberSequenceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(NextNumber::class, DatabaseNextNumber::class);
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * @return array<class-string>
     */
    public function provides(): array
    {
        return [NextNumber::class];
    }
}
