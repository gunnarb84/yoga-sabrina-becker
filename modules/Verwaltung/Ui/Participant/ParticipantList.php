<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Participant;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Participant\Participants\ParticipantsQuery;

#[Layout('verwaltung::layouts.app')]
final class ParticipantList extends Component
{
    /**
     * @var list<object{id: string, email: string, vorname: string, nachname: string, telefon: string|null, stadt: string|null}>
     */
    public array $participants = [];

    public function mount(ParticipantsQuery $query): void
    {
        $this->participants = $query->execute();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::participant.participant-list');
    }
}
