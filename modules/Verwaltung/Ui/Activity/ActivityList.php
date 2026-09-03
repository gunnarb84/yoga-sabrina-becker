<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\Activities\ActivitiesQuery;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\PublishActivity as PublishActivityOperation;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\Request as PublishActivityRequest;

#[Layout('verwaltung::layouts.app')]
final class ActivityList extends Component
{
    /**
     * @var list<object{id: string, typ: string, titel: string, kurzbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, status: string, veroeffentlicht: bool, anzahl_termine: int}>
     */
    public array $activities = [];

    public function mount(ActivitiesQuery $query): void
    {
        $this->activities = $query->execute();
    }

    public function publish(string $activityId, PublishActivityOperation $publish, ActivitiesQuery $query): void
    {
        $result = $publish->execute(new PublishActivityRequest($activityId));

        if ($result->isFailure()) {
            // In der Vollversion sollte ein Fehler angezeigt werden.
            return;
        }

        $this->activities = $query->execute();
    }

    public function render()
    {
        return view('verwaltung::activity.activity-list');
    }
}
