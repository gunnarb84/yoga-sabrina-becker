<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity\Request as UpdateActivityRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity\UpdateActivity as UpdateActivityOperation;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;

#[Layout('verwaltung::layouts.app')]
final class EditActivity extends Component
{
    public string $activityId = '';

    public string $type = 'kurs';

    public string $title = '';

    public string $shortDescription = '';

    public string $longDescription = '';

    public string $price = '';

    public int $maxParticipants = 10;

    public string $status = '';

    public string $message = '';

    public bool $saved = false;

    public function mount(string $id): void
    {
        $activity = Activity::findById($id);

        if ($activity === null) {
            abort(404);
        }

        $this->activityId = $activity->id;
        $this->type = $activity->typ->value;
        $this->title = $activity->titel;
        $this->shortDescription = $activity->kurzbeschreibung ?? '';
        $this->longDescription = $activity->langbeschreibung ?? '';
        $this->price = $activity->preis;
        $this->maxParticipants = $activity->maximale_teilnehmerzahl;
        $this->status = $activity->status->value;
    }

    public function save(UpdateActivityOperation $operation): void
    {
        $this->message = '';
        $this->saved = false;

        $type = ActivityType::tryFrom($this->type);

        if ($type === null) {
            $this->message = 'Bitte einen gueltigen Typ waehlen.';

            return;
        }

        $result = $operation->execute(new UpdateActivityRequest(
            activityId: $this->activityId,
            type: $type,
            title: $this->title,
            shortDescription: $this->shortDescription === '' ? null : $this->shortDescription,
            longDescription: $this->longDescription === '' ? null : $this->longDescription,
            price: $this->price,
            maxParticipants: $this->maxParticipants,
            image: null,
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Speichern: ' . json_encode($result->error());

            return;
        }

        $this->saved = true;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::activity.edit-activity', [
            'types' => array_map(fn (ActivityType $t): array => ['value' => $t->value, 'label' => ucfirst($t->value)], ActivityType::cases()),
            'statusLabel' => $this->status,
        ]);
    }
}
