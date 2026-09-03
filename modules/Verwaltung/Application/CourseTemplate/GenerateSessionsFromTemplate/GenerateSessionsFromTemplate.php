<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\GenerateSessionsFromTemplate;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\CourseTemplate\CourseTemplate;
use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Platform\Shared\Application\Result;

final readonly class GenerateSessionsFromTemplate
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $template = CourseTemplate::findById($request->templateId);

        if ($template === null) {
            return Result::failure('course_template.not_found');
        }

        if (! $this->isValid($template)) {
            return Result::failure('course_template.invalid');
        }

        return DB::transaction(function () use ($template): Result {
            $activity = new Activity([
                'typ' => ActivityType::Course->value,
                'titel' => $template->titel,
                'kurzbeschreibung' => $template->kurzbeschreibung,
                'langbeschreibung' => $template->langbeschreibung,
                'preis' => $template->preis,
                'maximale_teilnehmerzahl' => $template->maximale_teilnehmerzahl,
                'waehrung' => $template->waehrung,
                'veroeffentlicht' => false,
                'status' => ActivityStatus::Draft->value,
                'bild' => null,
            ]);

            $activity->save();
            $activity->slug = Str::slug($template->titel) . '-' . substr($activity->id, 0, 8);
            $activity->save();

            $weekday = $this->weekdayToCarbonConstant($template->wochentag);
            $startDate = $this->nextWeekdayDate($weekday);

            for ($i = 0; $i < $template->anzahl_termine; $i++) {
                $date = $startDate->copy()->addWeeks($i);
                $startsAt = Carbon::parse($date->format('Y-m-d') . ' ' . $template->startzeit);
                $endsAt = $startsAt->copy()->addMinutes($template->dauer_minuten);

                $session = new Session([
                    'aktivitaet_id' => $activity->id,
                    'beginn' => $startsAt,
                    'ende' => $endsAt,
                    'ort' => $template->ort,
                    'hinweis' => null,
                ]);

                $session->save();
            }

            return Result::success(new Response($activity->id));
        });
    }

    private function isValid(CourseTemplate $template): bool
    {
        if ($template->titel === '') {
            return false;
        }

        if (! in_array($template->wochentag, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], true)) {
            return false;
        }

        if ($template->startzeit === '' || \DateTimeImmutable::createFromFormat('H:i:s', $template->startzeit) === false) {
            return false;
        }

        if ($template->dauer_minuten < 1) {
            return false;
        }

        if ($template->anzahl_termine < 1) {
            return false;
        }

        if ((float) $template->preis < 0) {
            return false;
        }

        if ($template->maximale_teilnehmerzahl < 1) {
            return false;
        }

        return true;
    }

    private function weekdayToCarbonConstant(string $weekday): int
    {
        return match ($weekday) {
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
            default => Carbon::MONDAY,
        };
    }

    private function nextWeekdayDate(int $weekday): Carbon
    {
        $now = Carbon::now()->startOfDay();

        if ($now->dayOfWeekIso === $weekday) {
            return $now;
        }

        return $now->next($weekday);
    }
}
