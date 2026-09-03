<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Webseite\Application\Activity\ActivityDetail\ActivityDetailQuery;

#[Layout('webseite::layouts.app')]
final class ActivityDetail extends Component
{
    public string $slug = '';

    public ?object $activity = null;

    public function mount(string $slug, ActivityDetailQuery $query): void
    {
        $this->slug = $slug;

        $result = $query->execute($slug);

        if ($result->isSuccess()) {
            $this->activity = $result->value();
        }
    }

    public function render()
    {
        return view('webseite::activity.activity-detail');
    }
}
