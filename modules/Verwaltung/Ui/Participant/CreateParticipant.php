<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Participant;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\CreateParticipant as CreateParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\Request as CreateParticipantRequest;

#[Layout('verwaltung::layouts.app')]
final class CreateParticipant extends Component
{
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

    public string $message = '';

    public bool $created = false;

    public function save(CreateParticipantOperation $operation): void
    {
        $this->message = '';

        $result = $operation->execute(new CreateParticipantRequest(
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
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler beim Speichern: ' . json_encode($result->error());

            return;
        }

        $this->created = true;
        $this->reset(['email', 'firstName', 'lastName', 'addressLine1', 'addressLine2', 'postalCode', 'city', 'phone', 'dateOfBirth', 'healthNotes']);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Participant.create-participant');
    }
}
