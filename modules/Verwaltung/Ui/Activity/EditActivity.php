<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Activity;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity\Request as UpdateActivityRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity\UpdateActivity as UpdateActivityOperation;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\CreateSession as CreateSessionOperation;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\Request as CreateSessionRequest;
use Yoga\Modules\Verwaltung\Application\Session\DeleteSession\DeleteSession as DeleteSessionOperation;
use Yoga\Modules\Verwaltung\Application\Session\DeleteSession\Request as DeleteSessionRequest;
use Yoga\Modules\Verwaltung\Application\Session\SessionsByActivity\SessionsByActivityQuery;
use Yoga\Modules\Verwaltung\Application\Session\UpdateSession\Request as UpdateSessionRequest;
use Yoga\Modules\Verwaltung\Application\Session\UpdateSession\UpdateSession as UpdateSessionOperation;
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

    /**
     * @var list<object{id: string, beginn: string, ende: string, ort: string|null, hinweis: string|null, vergangen: bool}>
     */
    public array $sessions = [];

    public string $sessionId = '';

    public string $sessionStartsAt = '';

    public string $sessionEndsAt = '';

    public string $sessionLocation = '';

    public string $sessionNote = '';

    public string $sessionMessage = '';

    public function mount(string $id, SessionsByActivityQuery $query): void
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
        $this->sessions = $query->execute($this->activityId);
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

    public function addSession(CreateSessionOperation $operation, SessionsByActivityQuery $query): void
    {
        $this->sessionMessage = '';

        $result = $operation->execute(new CreateSessionRequest(
            activityId: $this->activityId,
            startsAt: $this->sessionStartsAt,
            endsAt: $this->sessionEndsAt,
            location: $this->sessionLocation === '' ? null : $this->sessionLocation,
            note: $this->sessionNote === '' ? null : $this->sessionNote,
        ));

        if ($result->isFailure()) {
            $this->sessionMessage = 'Fehler: ' . json_encode($result->error());

            return;
        }

        $this->resetSessionForm();
        $this->sessions = $query->execute($this->activityId);
    }

    public function startEditSession(string $sessionId): void
    {
        foreach ($this->sessions as $session) {
            if ($session->id === $sessionId) {
                $this->sessionId = $session->id;
                $this->sessionStartsAt = $session->beginn;
                $this->sessionEndsAt = $session->ende;
                $this->sessionLocation = $session->ort ?? '';
                $this->sessionNote = $session->hinweis ?? '';
                $this->sessionMessage = '';

                return;
            }
        }
    }

    public function cancelEditSession(): void
    {
        $this->resetSessionForm();
    }

    public function saveSession(UpdateSessionOperation $operation, SessionsByActivityQuery $query): void
    {
        $this->sessionMessage = '';

        if ($this->sessionId === '') {
            $this->sessionMessage = 'Kein Termin ausgewaehlt.';

            return;
        }

        $result = $operation->execute(new UpdateSessionRequest(
            sessionId: $this->sessionId,
            startsAt: $this->sessionStartsAt,
            endsAt: $this->sessionEndsAt,
            location: $this->sessionLocation === '' ? null : $this->sessionLocation,
            note: $this->sessionNote === '' ? null : $this->sessionNote,
        ));

        if ($result->isFailure()) {
            $this->sessionMessage = 'Fehler: ' . json_encode($result->error());

            return;
        }

        $this->resetSessionForm();
        $this->sessions = $query->execute($this->activityId);
    }

    public function deleteSession(string $sessionId, DeleteSessionOperation $operation, SessionsByActivityQuery $query): void
    {
        $this->sessionMessage = '';

        $result = $operation->execute(new DeleteSessionRequest($sessionId));

        if ($result->isFailure()) {
            $this->sessionMessage = 'Fehler: ' . json_encode($result->error());

            return;
        }

        $this->resetSessionForm();
        $this->sessions = $query->execute($this->activityId);
    }

    private function resetSessionForm(): void
    {
        $this->sessionId = '';
        $this->sessionStartsAt = '';
        $this->sessionEndsAt = '';
        $this->sessionLocation = '';
        $this->sessionNote = '';
        $this->sessionMessage = '';
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Activity.edit-activity', [
            'types' => array_map(fn (ActivityType $t): array => ['value' => $t->value, 'label' => ucfirst($t->value)], ActivityType::cases()),
            'statusLabel' => $this->status,
        ]);
    }
}
