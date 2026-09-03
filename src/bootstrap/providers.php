<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Livewire\LivewireServiceProvider;
use Yoga\Modules\Verwaltung\Persistence\VerwaltungServiceProvider;
use Yoga\Modules\Webseite\Persistence\WebseiteServiceProvider;
use Yoga\Platform\NumberSequence\Persistence\NumberSequenceServiceProvider;

return [
    AppServiceProvider::class,
    TinkerServiceProvider::class,
    LivewireServiceProvider::class,
    NumberSequenceServiceProvider::class,
    VerwaltungServiceProvider::class,
    WebseiteServiceProvider::class,
];
