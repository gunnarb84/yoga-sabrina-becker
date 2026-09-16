<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Yoga\Modules\Verwaltung\Application\Dashboard\ListDashboardMetrics\ListDashboardMetricsQuery;
use Yoga\Modules\Verwaltung\Application\Session\ListUpcomingSessions\ListUpcomingSessionsQuery;

#[Layout('verwaltung::layouts.app')]
final class Dashboard extends Component
{
    /**
     * @var list<object{id: string, aktivitaetId: string, beginn: string, titel: string, maxParticipants: int, confirmed: int, freeSeats: int}>
     */
    public array $sessions = [];

    /**
     * @var list<object{label: string, count: int|null, href: string}>
     */
    public array $tasks = [];

    /**
     * @var list<object{label: string, value: string}>
     */
    public array $metrics = [];

    /**
     * @var list<object{label: string, href: string}>
     */
    public array $shortcuts = [];

    public function mount(ListUpcomingSessionsQuery $upcomingSessions, ListDashboardMetricsQuery $metricsQuery): void
    {
        $this->sessions = $upcomingSessions->execute();

        $metrics = $metricsQuery->execute();

        $this->tasks = [
            (object) [
                'label' => 'Offene Rechnungen',
                'count' => $metrics->openInvoices,
                'href' => route('verwaltung.invoices'),
            ],
            (object) [
                'label' => 'Fehlgeschlagene E-Mails',
                'count' => $metrics->failedMessages,
                'href' => route('verwaltung.outbound-messages'),
            ],
            (object) [
                'label' => 'Wartelisteneinträge',
                'count' => $metrics->waitingListEntries,
                'href' => route('verwaltung.registrations'),
            ],
            (object) [
                'label' => 'Kontaktanfragen',
                'count' => null,
                'href' => route('verwaltung.contact-inquiries'),
            ],
        ];

        $this->metrics = [
            (object) [
                'label' => 'Anmeldungen im laufenden Monat',
                'value' => (string) $metrics->registrationsThisMonth,
            ],
            (object) [
                'label' => 'Bareinnahmen im laufenden Monat',
                'value' => number_format(
                    (float) $metrics->cashReceiptsMonth,
                    2,
                    ',',
                    '.',
                ).' '.$metrics->cashReceiptsMonthCurrency,
            ],
            (object) [
                'label' => 'Kassenbestand',
                'value' => number_format((float) $metrics->cashBalance, 2, ',', '.').' EUR',
            ],
        ];

        $this->shortcuts = [
            (object) [
                'label' => 'Bareinnahme erfassen',
                'href' => route('verwaltung.cash-receipt.record'),
            ],
            (object) [
                'label' => 'Aktivität anlegen',
                'href' => route('verwaltung.activity.create'),
            ],
            (object) [
                'label' => 'Anmeldungen',
                'href' => route('verwaltung.registrations'),
            ],
        ];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('verwaltung::dashboard');
    }
}
