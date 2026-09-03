<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Application\Activity\PublishedActivities\PublishedActivitiesQuery;

#[Layout('webseite::layouts.app')]
final class PublishedActivities extends Component
{
    /**
     * @var list<object{id: string, typ: string, titel: string, kurzbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, status: string, bild: string|null}>
     */
    public array $activities = [];

    public function mount(PublishedActivitiesQuery $query): void
    {
        $this->activities = $query->execute();
    }

    public function render()
    {
        return view('webseite::activity.published-activities');
    }
}
