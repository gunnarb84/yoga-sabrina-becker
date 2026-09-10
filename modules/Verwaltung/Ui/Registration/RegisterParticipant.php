<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Registration;

use Livewire\Attributes\Layout;
use Livewire\Component;
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

    public function mount(string $id, ParticipantsQuery $query): void
    {
        $this->activityId = $id;
        $this->participants = $query->execute();
    }

    public function register(RegisterParticipantOperation $operation): void
    {
        $this->message = '';

        $result = $operation->execute(new RegisterParticipantRequest(
            activityId: $this->activityId,
            participantId: $this->participantId,
            paymentMethod: $this->paymentMethod,
            source: RegisterParticipantRequest::SOURCE_ADMINISTRATION,
        ));

        if ($result->isFailure()) {
            $this->message = 'Fehler bei der Anmeldung: ' . json_encode($result->error());

            return;
        }

        $this->registered = true;
        $this->participantId = '';
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::Registration.register-participant', [
            'methods' => app(RegistrationPaymentMethodOptions::class)->execute(),
        ]);
    }
}
