<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Session;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\CreateSession as CreateSessionOperation;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\Request as CreateSessionRequest;

#[Layout('verwaltung::layouts.app')]
final class CreateSession extends Component
{
    public string $activityId = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public string $location = '';

    public string $note = '';

    public string $message = '';

    public bool $created = false;

    public function mount(string $id): void
    {
        $this->activityId = $id;
    }

    public function save(CreateSessionOperation $operation): void
    {
        $this->message = '';

        $result = $operation->execute(new CreateSessionRequest(
            activityId: $this->activityId,
            startsAt: $this->startsAt,
            endsAt: $this->endsAt,
            location: $this->location === '' ? null : $this->location,
            note: $this->note === '' ? null : $this->note,
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Speichern: ' . json_encode($result->error());

            return;
        }

        $this->created = true;
        $this->reset(['startsAt', 'endsAt', 'location', 'note']);
    }

    public function render()
    {
        return view('verwaltung::session.create-session');
    }
}
