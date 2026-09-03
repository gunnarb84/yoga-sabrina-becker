<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Registration;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\Request as UpsertParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\UpsertParticipant as UpsertParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant as RegisterParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\Request as SendConfirmationRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\SendRegistrationConfirmation;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Webseite\Application\Activity\ActivityDetail\ActivityDetailQuery;

#[Layout('webseite::layouts.app')]
final class RegisterForActivity extends Component
{
    public string $activityId = '';

    public ?object $activity = null;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $addressLine1 = '';

    public string $addressLine2 = '';

    public string $postalCode = '';

    public string $city = '';

    public string $phone = '';

    public string $dateOfBirth = '';

    public string $healthNotes = '';

    public bool $healthNotesConsent = false;

    public string $paymentMethod = '';

    public string $error = '';

    public bool $submitted = false;

    public bool $onWaitingList = false;

    public function mount(string $slug, ActivityDetailQuery $query): void
    {
        $result = $query->execute($slug);

        if ($result->isFailure()) {
            $this->redirect(route('activities'));

            return;
        }

        $this->activity = $result->value();
        $this->activityId = $this->activity->id;

        if ((float) $this->activity->preis === 0.0) {
            $this->paymentMethod = RegistrationPaymentMethod::Free->value;
        } else {
            $this->paymentMethod = RegistrationPaymentMethod::Transfer->value;
        }
    }

    public function submit(UpsertParticipantOperation $upsert, RegisterParticipantOperation $register, SendRegistrationConfirmation $confirmation): void
    {
        $this->error = '';

        if ($this->healthNotes !== '' && ! $this->healthNotesConsent) {
            $this->error = 'Bitte bestaetigen Sie die Speicherung der Gesundheitsinformationen.';

            return;
        }

        $method = RegistrationPaymentMethod::tryFrom($this->paymentMethod);

        if ($method === null) {
            $this->error = 'Bitte eine gueltige Zahlungsart waehlen.';

            return;
        }

        $upsertResult = $upsert->execute(new UpsertParticipantRequest(
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

        if ($upsertResult->isFailure()) {
            $this->error = 'Fehler beim Speichern der Teilnehmerdaten.';

            return;
        }

        $registerResult = $register->execute(new RegisterParticipantRequest(
            activityId: $this->activityId,
            participantId: $upsertResult->value()->participantId,
            paymentMethod: $method,
        ));

        if ($registerResult->isFailure()) {
            $this->error = 'Fehler bei der Anmeldung: '.($registerResult->error()['code'] ?? 'unknown');

            return;
        }

        $this->submitted = true;
        $this->onWaitingList = $registerResult->value()->onWaitingList;

        $confirmation->execute(new SendConfirmationRequest(
            registrationId: $registerResult->value()->registrationId,
        ));
    }

    public function render()
    {
        return view('webseite::registration.register-for-activity', [
            'paymentMethods' => $this->paymentMethodOptions(),
        ]);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function paymentMethodOptions(): array
    {
        $options = [];

        foreach (RegistrationPaymentMethod::cases() as $method) {
            if ($method === RegistrationPaymentMethod::Free) {
                continue;
            }

            $options[] = [
                'value' => $method->value,
                'label' => match ($method) {
                    RegistrationPaymentMethod::Cash => 'Barzahlung vor Ort',
                    RegistrationPaymentMethod::Transfer => 'Überweisung',
                    default => $method->value,
                },
            ];
        }

        return $options;
    }
}
