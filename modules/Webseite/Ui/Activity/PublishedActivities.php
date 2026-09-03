<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\PublicPublishedActivities\PublishedActivitiesQuery;

#[Layout('webseite::layouts.app')]
final class PublishedActivities extends Component
{
    /**
     * @var list<object>
     */
    public array $activities = [];

    public function mount(PublishedActivitiesQuery $query): void
    {
        $this->activities = $query->execute();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('webseite::activity.published-activities');
    }
}
