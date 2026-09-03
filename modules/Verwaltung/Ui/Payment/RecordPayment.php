<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Payment;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\RecordPayment as RecordPaymentOperation;
use Yoga\Modules\Verwaltung\Application\Payment\RecordPayment\Request as RecordPaymentRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\PaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentStatus;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;

#[Layout('verwaltung::layouts.app')]
final class RecordPayment extends Component
{
    public string $registrationId = '';

    public string $activityId = '';

    public string $amount = '';

    public string $paidAt = '';

    public string $recipient = '';

    public function mount(string $id): void
    {
        $this->registrationId = $id;
        $registration = Registration::findById($id);

        if ($registration === null || $registration->status === RegistrationStatus::Cancelled) {
            $this->redirect(route('verwaltung.activities'), navigate: true);

            return;
        }

        if ($registration->zahlungsstatus === RegistrationPaymentStatus::Paid) {
            $this->redirect(route('verwaltung.activity.registrations', ['id' => $registration->aktivitaet_id]), navigate: true);

            return;
        }

        if ($registration->zahlungsart === RegistrationPaymentMethod::Transfer) {
            $this->redirect(route('verwaltung.activity.registrations', ['id' => $registration->aktivitaet_id]), navigate: true);

            return;
        }

        $this->activityId = $registration->aktivitaet_id;
        $activity = Activity::findById($this->activityId);
        $participant = Participant::findById($registration->teilnehmer_id);

        $this->amount = $activity !== null ? (string) $activity->preis : '';
        $this->paidAt = now()->format('Y-m-d\TH:i');
        $this->recipient = $participant === null ? '' : trim($participant->vorname.' '.$participant->nachname);
    }

    public function save(RecordPaymentOperation $operation): void
    {
        $result = $operation->execute(new RecordPaymentRequest(
            registrationId: $this->registrationId,
            method: PaymentMethod::Cash,
            amount: $this->amount,
            paidAt: $this->paidAt,
            recipient: $this->recipient,
        ));

        if ($result->isFailure()) {
            return;
        }

        $this->redirect(route('verwaltung.activity.registrations', ['id' => $this->activityId]), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return View::make('verwaltung::payment.record-payment');
    }
}
