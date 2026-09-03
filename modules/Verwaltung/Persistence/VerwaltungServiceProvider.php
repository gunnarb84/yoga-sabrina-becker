<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Persistence;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Application\NumberSequence\VerwaltungNumberSequenceResolver;
use Yoga\Modules\Verwaltung\Ui\Activity\ActivityList;
use Yoga\Modules\Verwaltung\Ui\Activity\CreateActivity;
use Yoga\Modules\Verwaltung\Ui\Dashboard;
use Yoga\Modules\Verwaltung\Ui\Invoice\Invoices as InvoicesComponent;
use Yoga\Modules\Verwaltung\Ui\OutboundMessage\OutboundMessageDetail as OutboundMessageDetailComponent;
use Yoga\Modules\Verwaltung\Ui\OutboundMessage\OutboundMessageList as OutboundMessageListComponent;
use Yoga\Modules\Verwaltung\Ui\Participant\CreateParticipant as CreateParticipantComponent;
use Yoga\Modules\Verwaltung\Ui\Participant\ParticipantList;
use Yoga\Modules\Verwaltung\Ui\Payment\RecordPayment as RecordPaymentComponent;
use Yoga\Modules\Verwaltung\Ui\Registration\ActivityRegistrations;
use Yoga\Modules\Verwaltung\Ui\Registration\RegisterParticipant as RegisterParticipantComponent;
use Yoga\Modules\Verwaltung\Ui\Session\CreateSession;
use Yoga\Platform\NumberSequence\Application\NumberSequenceResolver;

final class VerwaltungServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->app->bind(NumberSequenceResolver::class, VerwaltungNumberSequenceResolver::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Ui', 'verwaltung');

        Livewire::component('verwaltung.dashboard', Dashboard::class);
        Livewire::component('verwaltung.aktivitaeten', ActivityList::class);
        Livewire::component('verwaltung.aktivitaet.neu', CreateActivity::class);
        Livewire::component('verwaltung.termin.neu', CreateSession::class);
        Livewire::component('verwaltung.teilnehmer', ParticipantList::class);
        Livewire::component('verwaltung.teilnehmer.neu', CreateParticipantComponent::class);
        Livewire::component('verwaltung.anmeldungen', ActivityRegistrations::class);
        Livewire::component('verwaltung.anmeldung.neu', RegisterParticipantComponent::class);
        Livewire::component('verwaltung.zahlung.neu', RecordPaymentComponent::class);
        Livewire::component('verwaltung.rechnungen', InvoicesComponent::class);
        Livewire::component('verwaltung.nachrichten', OutboundMessageListComponent::class);
        Livewire::component('verwaltung.nachrichten.detail', OutboundMessageDetailComponent::class);
    }
}
