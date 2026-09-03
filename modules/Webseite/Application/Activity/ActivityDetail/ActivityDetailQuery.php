<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Application\Activity\ActivityDetail;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Platform\Shared\Application\Result;

final readonly class ActivityDetailQuery
{
    public function execute(string $slug): Result
    {
        $activity = Activity::findBySlug($slug);

        if ($activity === null) {
            return Result::failure('activity.not_found');
        }

        if (! $activity->veroeffentlicht || $activity->status !== ActivityStatus::Published) {
            return Result::failure('activity.not_found');
        }

        $activityIdString = $activity->getAttribute('id');
        $activityIdBytes = Uuid::fromString($activityIdString)->getBytes();

        $sessions = DB::table('verwaltung_termine')
            ->select(['id', 'beginn', 'ende', 'ort', 'hinweis'])
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->where('beginn', '>=', now())
            ->orderBy('beginn')
            ->get();

        $confirmedCount = DB::table('verwaltung_anmeldungen')
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->where('status', RegistrationStatus::Confirmed->value)
            ->count();

        $freeSeats = max(0, (int) $activity->maximale_teilnehmerzahl - (int) $confirmedCount);

        $waitingListCount = DB::table('verwaltung_anmeldungen')
            ->whereRaw('aktivitaet_id = ?', [$activityIdBytes])
            ->where('status', RegistrationStatus::WaitingList->value)
            ->count();

        $isFull = $freeSeats === 0;
        $hasFutureSessions = $sessions->isNotEmpty();
        $isBookable = $hasFutureSessions
            && $activity->status !== ActivityStatus::Completed
            && $activity->status !== ActivityStatus::Cancelled;

        return Result::success((object) [
            'id' => $activityIdString,
            'slug' => $activity->slug,
            'typ' => $activity->typ,
            'typLabel' => $this->typeLabel($activity->typ),
            'titel' => $activity->titel,
            'kurzbeschreibung' => $activity->kurzbeschreibung,
            'langbeschreibung' => $activity->langbeschreibung,
            'preis' => $activity->preis,
            'maximale_teilnehmerzahl' => (int) $activity->maximale_teilnehmerzahl,
            'freie_plaetze' => $freeSeats,
            'warteliste_anzahl' => (int) $waitingListCount,
            'ausgebucht' => $isFull,
            'buchbar' => $isBookable,
            'bild' => $activity->bild,
            'termine' => $sessions->map(function (object $row): object {
                return (object) [
                    'id' => Uuid::fromBytes($row->id)->toString(),
                    'beginn' => $row->beginn,
                    'ende' => $row->ende,
                    'ort' => $row->ort,
                    'hinweis' => $row->hinweis,
                ];
            })->toArray(),
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
