<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Webseite\Application\Activity\ActivityOverview\ActivityOverviewQuery;

#[Layout('webseite::layouts.app')]
final class ActivityOverview extends Component
{
    /**
     * @var list<object{id: string, slug: string, typ: string, typLabel: string, titel: string, kurzbeschreibung: string|null, preis: string, bild: string|null, naechster_termin: string|null, freie_plaetze: int, warteliste_anzahl: int, ausgebucht: bool}>
     */
    public array $activities = [];

    public string $typeFilter = '';

    public function mount(ActivityOverviewQuery $query): void
    {
        $this->activities = $query->execute();
    }

    public function updatedTypeFilter(ActivityOverviewQuery $query): void
    {
        $this->activities = $query->execute($this->typeFilter);
    }

    public function render()
    {
        return view('webseite::activity.activity-overview', [
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function typeOptions(): array
    {
        return [
            ['value' => '', 'label' => 'Alle'],
            ['value' => ActivityType::Course->value, 'label' => 'Kurs'],
            ['value' => ActivityType::Event->value, 'label' => 'Event'],
            ['value' => ActivityType::Workshop->value, 'label' => 'Workshop'],
        ];
    }
}
