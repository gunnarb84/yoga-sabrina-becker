<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\PublicOverview\ActivityOverviewQuery;

#[Layout('webseite::layouts.app')]
final class ActivityOverview extends Component
{
    /**
     * @var list<object>
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

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('webseite::Activity.activity-overview', [
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
            ['value' => 'kurs', 'label' => 'Kurs'],
            ['value' => 'event', 'label' => 'Event'],
            ['value' => 'workshop', 'label' => 'Workshop'],
        ];
    }
}
