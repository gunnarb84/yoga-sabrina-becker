<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Ui\Registration;

use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Activity\PublicDetail\ActivityDetailQuery;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\Request as UpsertParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Participant\UpsertParticipant\UpsertParticipant as UpsertParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant as RegisterParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\Request as SendConfirmationRequest;
use Yoga\Modules\Verwaltung\Application\Registration\SendRegistrationConfirmation\SendRegistrationConfirmation;

#[Layout('webseite::layouts.app')]
final class RegisterForActivity extends Component
{
    public string $activityId = '';

    /**
     * @var object{id: string, slug: string, typ: string, typLabel: string, titel: string, kurzbeschreibung: string|null, langbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, freie_plaetze: int, warteliste_anzahl: int, ausgebucht: bool, buchbar: bool, bild: string|null, termine: list<object{id: string, beginn: \Carbon\Carbon, ende: \Carbon\Carbon, ort: string|null, hinweis: string|null}>}|null
     */
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

    public bool $photoConsent = false;

    public bool $videoConsent = false;

    public bool $privacyConsent = false;

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

        $this->activity = $result->unwrap();
        $this->activityId = $this->activity->id;

        if ((float) $this->activity->preis === 0.0) {
            $this->paymentMethod = 'kostenlos';
        } else {
            $this->paymentMethod = 'ueberweisung';
        }
    }

    public function submit(UpsertParticipantOperation $upsert, RegisterParticipantOperation $register, SendRegistrationConfirmation $confirmation): void
    {
        $this->error = '';

        if (! $this->privacyConsent) {
            $this->error = 'Bitte bestätigen Sie die Datenverarbeitung gemäß Datenschutzerklärung.';

            return;
        }

        if ($this->healthNotes !== '' && ! $this->healthNotesConsent) {
            $this->error = 'Bitte bestaetigen Sie die Speicherung der Gesundheitsinformationen.';

            return;
        }

        if (! in_array($this->paymentMethod, ['bar', 'ueberweisung', 'kostenlos'], true)) {
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
            photoConsent: $this->photoConsent,
            videoConsent: $this->videoConsent,
        ));

        if ($upsertResult->isFailure()) {
            $this->error = 'Fehler beim Speichern der Teilnehmerdaten.';

            return;
        }

        $registerResult = $register->execute(new RegisterParticipantRequest(
            activityId: $this->activityId,
            participantId: $upsertResult->unwrap()->participantId,
            paymentMethod: $this->paymentMethod,
            privacyConsent: $this->privacyConsent,
        ));

        if ($registerResult->isFailure()) {
            $this->error = $this->errorFor((string) ($registerResult->error()['code'] ?? 'unknown'));

            return;
        }

        $this->submitted = true;
        $this->onWaitingList = $registerResult->unwrap()->onWaitingList;

        $confirmation->execute(new SendConfirmationRequest(
            registrationId: $registerResult->unwrap()->registrationId,
        ));
    }

    private function errorFor(string $code): string
    {
        return match ($code) {
            'registration.already_registered' => 'Sie sind für diese Veranstaltung bereits angemeldet.',
            'registration.privacy_consent_required' => 'Bitte bestätigen Sie die Datenverarbeitung gemäß Datenschutzerklärung.',
            'registration.invalid_payment_method' => 'Bitte eine gültige Zahlungsart wählen.',
            'activity.not_found' => 'Diese Veranstaltung existiert nicht.',
            'activity.not_bookable' => 'Diese Veranstaltung ist zurzeit nicht buchbar.',
            'participant.not_found' => 'Die Teilnehmerdaten konnten nicht zugeordnet werden.',
            default => 'Fehler bei der Anmeldung. Bitte versuchen Sie es erneut.',
        };
    }

    /**
     * Für Minderjährige unterschreiben die Sorgeberechtigten den Papierbogen;
     * die Online-Einwilligung wird deshalb nicht angeboten.
     */
    public function isMinor(): bool
    {
        return $this->dateOfBirth !== '' && Carbon::parse($this->dateOfBirth)->age < 18;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('webseite::Registration.register-for-activity', [
            'paymentMethods' => $this->paymentMethodOptions(),
        ]);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function paymentMethodOptions(): array
    {
        return [
            ['value' => 'bar', 'label' => 'Barzahlung vor Ort'],
            ['value' => 'ueberweisung', 'label' => 'Überweisung'],
        ];
    }
}
