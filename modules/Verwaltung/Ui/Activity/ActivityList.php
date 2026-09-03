<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\Activities\ActivitiesQuery;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\PublishActivity as PublishActivityOperation;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\Request as PublishActivityRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UnpublishActivity\Request as UnpublishActivityRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UnpublishActivity\UnpublishActivity as UnpublishActivityOperation;

#[Layout('verwaltung::layouts.app')]
final class ActivityList extends Component
{
    /**
     * @var list<object{id: string, typ: string, titel: string, kurzbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, status: string, veroeffentlicht: bool, anzahl_termine: int}>
     */
    public array $activities = [];

    public string $message = '';

    public function mount(ActivitiesQuery $query): void
    {
        $this->activities = $query->execute();
    }

    public function publish(string $activityId, PublishActivityOperation $publish, ActivitiesQuery $query): void
    {
        $this->message = '';
        $result = $publish->execute(new PublishActivityRequest($activityId));

        if ($result->isFailure()) {
            $this->message = 'Fehler: ' . json_encode($result->error());

            return;
        }

        $this->activities = $query->execute();
    }

    public function unpublish(string $activityId, UnpublishActivityOperation $unpublish, ActivitiesQuery $query): void
    {
        $this->message = '';
        $result = $unpublish->execute(new UnpublishActivityRequest($activityId));

        if ($result->isFailure()) {
            $this->message = 'Fehler: ' . json_encode($result->error());

            return;
        }

        $this->activities = $query->execute();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::activity.activity-list');
    }
}
