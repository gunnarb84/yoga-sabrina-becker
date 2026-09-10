<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Participant;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Participant\ParticipantEdit\ParticipantEditQuery;
use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\Request as UpdateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpdateParticipant\UpdateParticipant as UpdateParticipantOperation;

#[Layout('verwaltung::layouts.app')]
final class EditParticipant extends Component
{
    public string $participantId = '';

    public string $email = '';

    public string $firstName = '';

    public string $lastName = '';

    public string $addressLine1 = '';

    public string $addressLine2 = '';

    public string $postalCode = '';

    public string $city = '';

    public string $phone = '';

    public string $dateOfBirth = '';

    public string $healthNotes = '';

    public bool $healthNotesConsent = false;

    public string $message = '';

    public bool $saved = false;

    public bool $showHealthNotes = false;

    /**
     * @var list<object{id: string, aktivitaet_titel: string, status: string, anmeldedatum: string}>
     */
    public array $registrations = [];

    public function mount(string $id, ParticipantEditQuery $query): void
    {
        $participant = $query->execute($id);

        if ($participant === null) {
            abort(404);
        }

        $this->participantId = $participant->id;
        $this->email = $participant->email ?? '';
        $this->firstName = $participant->vorname;
        $this->lastName = $participant->nachname;
        $this->addressLine1 = $participant->adresszeile_1;
        $this->addressLine2 = $participant->adresszeile_2;
        $this->postalCode = $participant->postleitzahl;
        $this->city = $participant->stadt;
        $this->phone = $participant->telefon;
        $this->dateOfBirth = $participant->geburtsdatum;
        $this->healthNotes = $participant->gesundheitsinformationen;
        $this->healthNotesConsent = $participant->gesundheitsinformationen_einwilligung;
        $this->registrations = $participant->anmeldungen;
    }

    public function save(UpdateParticipantOperation $operation): void
    {
        $this->message = '';
        $this->saved = false;

        $result = $operation->execute(new UpdateParticipantRequest(
            participantId: $this->participantId,
            email: $this->email,
            firstName: $this->firstName,
            lastName: $this->lastName,
            addressLine1: $this->addressLine1 === '' ? null : $this->addressLine1,
            addressLine2: $this->addressLine2 === '' ? null : $this->addressLine2,
            postalCode: $this->postalCode === '' ? null : $this->postalCode,
            city: $this->city === '' ? null : $this->city,
            phone: $this->phone === '' ? null : $this->phone,
            dateOfBirth: $this->dateOfBirth === '' ? null : $this->dateOfBirth,
            healthNotes: $this->healthNotes === '' ? null : $this->healthNotes,
            healthNotesConsent: $this->healthNotesConsent,
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Speichern: ' . json_encode($result->error());

            return;
        }

        $this->saved = true;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Participant.edit-participant');
    }
}
