<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Laravel\Tinker\TinkerServiceProvider;
use Livewire\LivewireServiceProvider;
use Yoga\Modules\Verwaltung\Application\VerwaltungServiceProvider as VerwaltungApplicationServiceProvider;
use Yoga\Modules\Verwaltung\Persistence\VerwaltungServiceProvider as VerwaltungPersistenceServiceProvider;
use Yoga\Modules\Verwaltung\Ui\VerwaltungServiceProvider as VerwaltungUiServiceProvider;
use Yoga\Modules\Webseite\Persistence\WebseiteServiceProvider as WebseitePersistenceServiceProvider;
use Yoga\Modules\Webseite\Ui\WebseiteServiceProvider as WebseiteUiServiceProvider;
use Yoga\Platform\NumberSequence\Persistence\NumberSequenceServiceProvider;

return [
    AppServiceProvider::class,
    TinkerServiceProvider::class,
    LivewireServiceProvider::class,
    NumberSequenceServiceProvider::class,
    VerwaltungApplicationServiceProvider::class,
    VerwaltungPersistenceServiceProvider::class,
    VerwaltungUiServiceProvider::class,
    WebseitePersistenceServiceProvider::class,
    WebseiteUiServiceProvider::class,
];
