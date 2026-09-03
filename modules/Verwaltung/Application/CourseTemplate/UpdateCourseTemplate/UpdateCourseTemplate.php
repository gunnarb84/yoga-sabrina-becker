<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\CourseTemplate\UpdateCourseTemplate;

use Yoga\Modules\Verwaltung\Domain\CourseTemplate\CourseTemplate;
use Yoga\Platform\Shared\Application\Result;

final readonly class UpdateCourseTemplate
{
    /** @return Result<Response> */
    public function execute(Request $request): Result
    {
        $template = CourseTemplate::findById($request->templateId);

        if ($template === null) {
            return Result::failure('course_template.not_found');
        }

        $validation = $this->validate($request);

        if ($validation !== null) {
            return Result::failure($validation);
        }

        $template->titel = $request->title;
        $template->wochentag = $request->weekday;
        $startzeit = $this->normalizeTime($request->startTime);
        \assert($startzeit !== null);
        $template->startzeit = $startzeit;
        $template->dauer_minuten = $request->durationMinutes;
        $template->anzahl_termine = $request->sessionCount;
        $template->preis = $request->price;
        $template->waehrung = $request->currency;
        $template->maximale_teilnehmerzahl = $request->maxParticipants;
        $template->kurzbeschreibung = $request->shortDescription;
        $template->langbeschreibung = $request->longDescription;
        $template->ort = $request->location;

        $template->save();

        return Result::success(new Response($template->id));
    }

    private function validate(Request $request): ?string
    {
        if ($request->title === '') {
            return 'course_template.title_empty';
        }

        if (! in_array($request->weekday, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], true)) {
            return 'course_template.weekday_invalid';
        }

        if ($request->startTime === '' || $this->normalizeTime($request->startTime) === null) {
            return 'course_template.start_time_invalid';
        }

        if ($request->durationMinutes < 1) {
            return 'course_template.duration_minutes_too_low';
        }

        if ($request->sessionCount < 1) {
            return 'course_template.session_count_too_low';
        }

        if ((float) $request->price < 0) {
            return 'course_template.price_negative';
        }

        if ($request->maxParticipants < 1) {
            return 'course_template.max_participants_too_low';
        }

        return null;
    }

    private function normalizeTime(string $time): ?string
    {
        if ($time === '') {
            return null;
        }

        $parsed = \DateTimeImmutable::createFromFormat('H:i', $time);

        if ($parsed === false) {
            $parsed = \DateTimeImmutable::createFromFormat('H:i:s', $time);
        }

        if ($parsed === false) {
            return null;
        }

        return $parsed->format('H:i:s');
    }
}
