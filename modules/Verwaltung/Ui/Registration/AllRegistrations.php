<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Registration;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\Activities\ActivitiesQuery;
use Yoga\Modules\Verwaltung\Application\Registration\AllRegistrations\AllRegistrationsQuery;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;

#[Layout('verwaltung::layouts.app')]
final class AllRegistrations extends Component
{
    /**
     * @var list<object{id: string, aktivitaet_id: string, aktivitaet_titel: string, teilnehmer_id: string, teilnehmer_name: string, email: string, status: string, zahlungsart: string, angemeldet_am: string, freie_plaetze: int, warteliste_anzahl: int}>
     */
    public array $registrations = [];

    /**
     * @var list<object{id: string, titel: string}>
     */
    public array $activities = [];

    public string $filterActivityId = '';

    public string $filterStatus = '';

    public string $filterSearch = '';

    public function mount(AllRegistrationsQuery $registrationsQuery, ActivitiesQuery $activitiesQuery): void
    {
        $this->activities = $activitiesQuery->execute();
        $this->registrations = $registrationsQuery->execute();
    }

    public function applyFilters(AllRegistrationsQuery $query): void
    {
        $this->registrations = $query->execute(
            activityId: $this->filterActivityId === '' ? null : $this->filterActivityId,
            status: $this->filterStatus === '' ? null : $this->filterStatus,
            search: $this->filterSearch === '' ? null : $this->filterSearch,
        );
    }

    public function resetFilters(AllRegistrationsQuery $query): void
    {
        $this->filterActivityId = '';
        $this->filterStatus = '';
        $this->filterSearch = '';
        $this->registrations = $query->execute();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Registration.all-registrations', [
            'statusOptions' => array_map(fn (RegistrationStatus $s): array => ['value' => $s->value, 'label' => ucfirst($s->value)], RegistrationStatus::cases()),
            'typeLabel' => fn (string $payment): string => match ($payment) {
                'bar' => 'Bar',
                'ueberweisung' => 'Überweisung',
                'kostenlos' => 'Kostenlos',
                default => $payment,
            },
        ]);
    }
}
