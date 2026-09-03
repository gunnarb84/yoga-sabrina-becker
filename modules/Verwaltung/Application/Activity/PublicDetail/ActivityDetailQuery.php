<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity\PublicDetail;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Platform\Shared\Application\Result;

final readonly class ActivityDetailQuery
{
    /**
     * @return Result<object{id: string, slug: string, typ: string, typLabel: string, titel: string, kurzbeschreibung: string|null, langbeschreibung: string|null, preis: string, maximale_teilnehmerzahl: int, freie_plaetze: int, warteliste_anzahl: int, ausgebucht: bool, buchbar: bool, bild: string|null, termine: list<object{id: string, beginn: \Carbon\Carbon, ende: \Carbon\Carbon, ort: string|null, hinweis: string|null}>}>
     */
    public function execute(string $slug): Result
    {
        $activity = Activity::findBySlug($slug);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        if (! $activity->veroeffentlicht || $activity->status !== ActivityStatus::Published) {
            return Result::failure('activity.not_found');
        }

        $activityIdString = $activity->id;
        $activityIdBytes = Uuid::fromString($activityIdString)->getBytes();

        $sessions = Session::select(['id', 'beginn', 'ende', 'ort', 'hinweis'])
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->where('beginn', '>=', now())
            ->orderBy('beginn')
            ->get();

        $confirmedCount = DB::table('verwaltung_anmeldungen')
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->where('status', RegistrationStatus::Confirmed->value)
            ->count();

        $freeSeats = max(0, $activity->maximale_teilnehmerzahl - $confirmedCount);

        $waitingListCount = DB::table('verwaltung_anmeldungen')
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->where('status', RegistrationStatus::WaitingList->value)
            ->count();

        $isFull = $freeSeats === 0;
        $hasFutureSessions = $sessions->isNotEmpty();
        $isBookable = $hasFutureSessions;

        $type = $activity->typ;

        $termine = [];
        foreach ($sessions as $session) {
            $termine[] = (object) [
                'id' => $session->id,
                'beginn' => $session->beginn,
                'ende' => $session->ende,
                'ort' => $session->ort,
                'hinweis' => $session->hinweis,
            ];
        }

        return Result::success((object) [
            'id' => $activityIdString,
            'slug' => (string) $activity->slug,
            'typ' => $type->value,
            'typLabel' => $this->typeLabel($type),
            'titel' => $activity->titel,
            'kurzbeschreibung' => $activity->kurzbeschreibung,
            'langbeschreibung' => $activity->langbeschreibung,
            'preis' => $activity->preis,
            'maximale_teilnehmerzahl' => $activity->maximale_teilnehmerzahl,
            'freie_plaetze' => $freeSeats,
            'warteliste_anzahl' => $waitingListCount,
            'ausgebucht' => $isFull,
            'buchbar' => $isBookable,
            'bild' => $activity->bild,
            'termine' => $termine,
        ]);
    }

    private function typeLabel(ActivityType $type): string
    {
        return match ($type) {
            ActivityType::Course => 'Kurs',
            ActivityType::Event => 'Event',
            ActivityType::Workshop => 'Workshop',
        };
    }
}
