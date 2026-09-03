<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Persistence;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Yoga\Modules\Webseite\Domain\Navigation\NavigationItem;
use Yoga\Modules\Webseite\Ui\Activity\ActivityDetail;
use Yoga\Modules\Webseite\Ui\Activity\ActivityOverview;
use Yoga\Modules\Webseite\Ui\Activity\PublishedActivities;
use Yoga\Modules\Webseite\Ui\Page\StaticPage;
use Yoga\Modules\Webseite\Ui\Registration\RegisterForActivity;

final class WebseiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Ui', 'webseite');

        Livewire::component('webseite.aktivitaeten', PublishedActivities::class);
        Livewire::component('webseite.aktivitaeten.uebersicht', ActivityOverview::class);
        Livewire::component('webseite.aktivitaet', ActivityDetail::class);
        Livewire::component('webseite.anmeldung', RegisterForActivity::class);
        Livewire::component('webseite.seite', StaticPage::class);

        View::composer('webseite::layouts.app', function ($view): void {
            $view->with('navigation', NavigationItem::where('aktiv', true)
                ->orderBy('sortierung')
                ->get());
        });
    }
}
