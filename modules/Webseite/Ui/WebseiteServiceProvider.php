<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;
use Livewire\Livewire;
use Yoga\Modules\Webseite\Application\Navigation\ListNavigationItems;
use Yoga\Modules\Webseite\Ui\Activity\ActivityDetail;
use Yoga\Modules\Webseite\Ui\Activity\ActivityOverview;
use Yoga\Modules\Webseite\Ui\Activity\PublishedActivities;
use Yoga\Modules\Webseite\Ui\ContactInquiry\ContactInquiryDetail;
use Yoga\Modules\Webseite\Ui\ContactInquiry\ContactInquiryForm;
use Yoga\Modules\Webseite\Ui\ContactInquiry\ContactInquiryList;
use Yoga\Modules\Webseite\Ui\Page\StaticPage;
use Yoga\Modules\Webseite\Ui\Registration\RegisterForActivity;

final class WebseiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__, 'webseite');

        Livewire::component('webseite.aktivitaeten', PublishedActivities::class);
        Livewire::component('webseite.aktivitaeten.uebersicht', ActivityOverview::class);
        Livewire::component('webseite.aktivitaet', ActivityDetail::class);
        Livewire::component('webseite.anmeldung', RegisterForActivity::class);
        Livewire::component('webseite.seite', StaticPage::class);
        Livewire::component('webseite.kontaktanfrage', ContactInquiryForm::class);
        Livewire::component('webseite.kontaktanfragen', ContactInquiryList::class);
        Livewire::component('webseite.kontaktanfrage.detail', ContactInquiryDetail::class);

        View::composer('webseite::layouts.app', function (ViewContract $view): void {
            $view->with('navigation', app(ListNavigationItems::class)->execute());
        });
    }
}
