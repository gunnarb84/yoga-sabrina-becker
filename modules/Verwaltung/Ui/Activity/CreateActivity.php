<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\CreateActivity\CreateActivity as CreateActivityOperation;
use Yoga\Modules\Verwaltung\Application\Activity\CreateActivity\Request as CreateActivityRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;

#[Layout('verwaltung::layouts.app')]
final class CreateActivity extends Component
{
    public string $type = 'kurs';

    public string $title = '';

    public string $shortDescription = '';

    public string $longDescription = '';

    public string $price = '';

    public int $maxParticipants = 10;

    public string $message = '';

    public bool $created = false;

    public function save(CreateActivityOperation $operation): void
    {
        $this->message = '';

        $type = ActivityType::tryFrom($this->type);

        if ($type === null) {
            $this->message = 'Bitte einen gueltigen Typ waehlen.';

            return;
        }

        $result = $operation->execute(new CreateActivityRequest(
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

        $this->created = true;
        $this->reset(['type', 'title', 'shortDescription', 'longDescription', 'price', 'maxParticipants']);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Activity.create-activity', [
            'types' => array_map(fn (ActivityType $t): array => ['value' => $t->value, 'label' => ucfirst($t->value)], ActivityType::cases()),
        ]);
    }
}
