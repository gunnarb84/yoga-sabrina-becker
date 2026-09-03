<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Registration;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\CancelRegistration as CancelRegistrationOperation;
use Yoga\Modules\Verwaltung\Application\Registration\CancelRegistration\Request as CancelRegistrationRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegistrationsByActivity\RegistrationsByActivityQuery;

#[Layout('verwaltung::layouts.app')]
final class ActivityRegistrations extends Component
{
    public string $activityId = '';

    /**
     * @var list<object{id: string, teilnehmer_id: string, teilnehmer_name: string, email: string, status: string, angemeldet_am: string, zahlungsart: string, rang: int|null}>
     */
    public array $registrations = [];

    public string $message = '';

    public function mount(string $id, RegistrationsByActivityQuery $query): void
    {
        $this->activityId = $id;
        $this->registrations = $query->execute($id);
    }

    public function cancel(string $registrationId, CancelRegistrationOperation $operation, RegistrationsByActivityQuery $query): void
    {
        $this->message = '';
        $result = $operation->execute(new CancelRegistrationRequest($registrationId));

        if ($result->isFailure()) {
            $error = $result->error();

            $this->message = match ($error !== null ? $error['code'] : '') {
                'registration.not_found' => 'Die Anmeldung wurde nicht gefunden.',
                'registration.already_cancelled' => 'Die Anmeldung ist bereits storniert.',
                default => 'Die Stornierung ist fehlgeschlagen.',
            };

            return;
        }

        $this->registrations = $query->execute($this->activityId);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::registration.activity-registrations');
    }
}
