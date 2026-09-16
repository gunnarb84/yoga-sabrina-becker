<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Registration;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\CreateParticipant as CreateParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\Request as CreateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\Participants\ParticipantsQuery;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant as RegisterParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegistrationPaymentMethodOptions;

#[Layout('verwaltung::layouts.app')]
final class RegisterParticipant extends Component
{
    public string $activityId = '';

    public string $participantId = '';

    public string $paymentMethod = 'bar';

    public string $message = '';

    public bool $registered = false;

    /**
     * @var list<object{id: string, email: string, vorname: string, nachname: string}>
     */
    public array $participants = [];

    public bool $showNewParticipant = false;

    public string $newFirstName = '';

    public string $newLastName = '';

    public string $newEmail = '';

    public string $newPhone = '';

    public function mount(string $id, ParticipantsQuery $query): void
    {
        $this->activityId = $id;
        $this->participants = $query->execute();
    }

    public function toggleNewParticipant(): void
    {
        $this->showNewParticipant = ! $this->showNewParticipant;
        $this->message = '';
    }

    public function register(
        CreateParticipantOperation $createParticipant,
        RegisterParticipantOperation $operation,
        ParticipantsQuery $query,
    ): void {
        $this->message = '';

        $participantId = $this->participantId;

        if ($this->showNewParticipant) {
            $createResult = $createParticipant->execute(new CreateParticipantRequest(
                email: $this->newEmail !== '' ? $this->newEmail : null,
                firstName: $this->newFirstName,
                lastName: $this->newLastName,
                addressLine1: null,
                addressLine2: null,
                postalCode: null,
                city: null,
                phone: $this->newPhone !== '' ? $this->newPhone : null,
                dateOfBirth: null,
                healthNotes: null,
            ));

            if ($createResult->isFailure()) {
                $this->message = 'Fehler beim Anlegen des Teilnehmers: '
                    .($createResult->error()['code'] ?? 'unknown');

                return;
            }

            $participantId = $createResult->unwrap()->participantId;
        }

        $result = $operation->execute(new RegisterParticipantRequest(
            activityId: $this->activityId,
            participantId: $participantId,
            paymentMethod: $this->paymentMethod,
            source: RegisterParticipantRequest::SOURCE_ADMINISTRATION,
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler bei der Anmeldung: '.($result->error()['code'] ?? 'unknown');

            return;
        }

        $this->registered = true;
        $this->participantId = '';
        $this->resetNewParticipantForm();
        $this->participants = $query->execute();
    }

    private function resetNewParticipantForm(): void
    {
        $this->showNewParticipant = false;
        $this->newFirstName = '';
        $this->newLastName = '';
        $this->newEmail = '';
        $this->newPhone = '';
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Registration.register-participant', [
            'methods' => app(RegistrationPaymentMethodOptions::class)->execute(),
        ]);
    }
}
