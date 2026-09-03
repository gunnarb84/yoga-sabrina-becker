<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F1 Veranstaltungen verwalten / S5 Wiederkehrende Kurse aus Vorlage erzeugen
 *
 * Geprüfte Kriterien:
 * - Kursvorlagen können verwaltet werden (anlegen, bearbeiten, löschen).
 * - Eine Vorlage hat die Pflichtfelder Titel, Wochentag, Startzeit, Dauer, Anzahl Termine, Preis, Max. Teilnehmer.
 * - GenerateSessionsFromTemplate erzeugt eine neue Veranstaltung vom Typ Kurs im Status Entwurf.
 * - Die Termine beginnen am nächstmöglichen Wochentag ab heute zur angegebenen Startzeit.
 * - Jedes Terminende ergibt sich aus Startzeit + Dauer.
 * - Die erzeugte Veranstaltung ist vor Veröffentlichung manuell korrigierbar.
 * - Der Vorgang scheitert mit INVALID_TEMPLATE, wenn die Vorlage ungültige Werte enthält.
 */

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\CreateCourseTemplate\CreateCourseTemplate;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\CreateCourseTemplate\Request as CreateRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate\DeleteCourseTemplate;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\DeleteCourseTemplate\Request as DeleteRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\GenerateSessionsFromTemplate\GenerateSessionsFromTemplate;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\GenerateSessionsFromTemplate\Request as GenerateRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\UpdateCourseTemplate\Request as UpdateRequest;
use Yoga\Modules\Verwaltung\Application\CourseTemplate\UpdateCourseTemplate\UpdateCourseTemplate;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\CourseTemplate\CourseTemplate;
use Yoga\Modules\Verwaltung\Domain\Session\Session;

beforeEach(function (): void {
    $this->template = new CourseTemplate([
        'titel' => 'Morgenyoga',
        'wochentag' => 'wednesday',
        'startzeit' => '08:00:00',
        'dauer_minuten' => 60,
        'anzahl_termine' => 4,
        'preis' => '90.00',
        'waehrung' => 'EUR',
        'maximale_teilnehmerzahl' => 12,
        'ort' => 'Studio 1',
    ]);
    $this->template->save();
});

it('creates a course template with required fields', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Abendyoga',
        weekday: 'friday',
        startTime: '18:00',
        durationMinutes: 90,
        sessionCount: 8,
        price: '120.00',
        maxParticipants: 10,
    ));

    expect($result->isSuccess())->toBeTrue();

    $template = CourseTemplate::findById($result->unwrap()->templateId);
    expect($template)->not->toBeNull();
    expect($template->titel)->toBe('Abendyoga');
    expect($template->wochentag)->toBe('friday');
    expect($template->startzeit)->toBe('18:00:00');
    expect($template->dauer_minuten)->toBe(90);
    expect($template->anzahl_termine)->toBe(8);
    expect($template->preis)->toBe('120.0000');
    expect($template->maximale_teilnehmerzahl)->toBe(10);
});

it('fails with title_empty when the title is empty', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: '',
        weekday: 'friday',
        startTime: '18:00',
        durationMinutes: 60,
        sessionCount: 4,
        price: '90.00',
        maxParticipants: 10,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.title_empty');
});

it('fails with weekday_invalid for an invalid weekday', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Yoga',
        weekday: 'invalid',
        startTime: '18:00',
        durationMinutes: 60,
        sessionCount: 4,
        price: '90.00',
        maxParticipants: 10,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.weekday_invalid');
});

it('fails with start_time_invalid for an invalid start time', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Yoga',
        weekday: 'friday',
        startTime: 'not-a-time',
        durationMinutes: 60,
        sessionCount: 4,
        price: '90.00',
        maxParticipants: 10,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.start_time_invalid');
});

it('fails with duration_minutes_too_low when duration is below one minute', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Yoga',
        weekday: 'friday',
        startTime: '18:00',
        durationMinutes: 0,
        sessionCount: 4,
        price: '90.00',
        maxParticipants: 10,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.duration_minutes_too_low');
});

it('fails with session_count_too_low when session count is below one', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Yoga',
        weekday: 'friday',
        startTime: '18:00',
        durationMinutes: 60,
        sessionCount: 0,
        price: '90.00',
        maxParticipants: 10,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.session_count_too_low');
});

it('fails with price_negative when the price is negative', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Yoga',
        weekday: 'friday',
        startTime: '18:00',
        durationMinutes: 60,
        sessionCount: 4,
        price: '-10.00',
        maxParticipants: 10,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.price_negative');
});

it('fails with max_participants_too_low when max participants is below one', function (): void {
    $operation = new CreateCourseTemplate();
    $result = $operation->execute(new CreateRequest(
        title: 'Yoga',
        weekday: 'friday',
        startTime: '18:00',
        durationMinutes: 60,
        sessionCount: 4,
        price: '90.00',
        maxParticipants: 0,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.max_participants_too_low');
});

it('updates a course template', function (): void {
    $operation = new UpdateCourseTemplate();
    $result = $operation->execute(new UpdateRequest(
        templateId: $this->template->id,
        title: 'Morgenyoga aktualisiert',
        weekday: 'thursday',
        startTime: '09:00',
        durationMinutes: 75,
        sessionCount: 6,
        price: '100.00',
        maxParticipants: 8,
    ));

    expect($result->isSuccess())->toBeTrue();

    $template = CourseTemplate::findById($this->template->id);
    expect($template)->not->toBeNull();
    expect($template->titel)->toBe('Morgenyoga aktualisiert');
    expect($template->wochentag)->toBe('thursday');
    expect($template->startzeit)->toBe('09:00:00');
    expect($template->dauer_minuten)->toBe(75);
    expect($template->anzahl_termine)->toBe(6);
    expect($template->maximale_teilnehmerzahl)->toBe(8);
});

it('deletes a course template', function (): void {
    $operation = new DeleteCourseTemplate();
    $result = $operation->execute(new DeleteRequest($this->template->id));

    expect($result->isSuccess())->toBeTrue();
    expect(CourseTemplate::findById($this->template->id))->toBeNull();
});

it('generates a course activity with sessions from a valid template', function (): void {
    $operation = new GenerateSessionsFromTemplate();
    $result = $operation->execute(new GenerateRequest($this->template->id));

    expect($result->isSuccess())->toBeTrue();

    $activity = Activity::findById($result->unwrap()->activityId);
    expect($activity)->not->toBeNull();
    expect($activity->typ)->toBe(ActivityType::Course);
    expect($activity->status)->toBe(ActivityStatus::Draft);
    expect($activity->veroeffentlicht)->toBeFalse();
    expect($activity->titel)->toBe('Morgenyoga');
    expect($activity->preis)->toBe('90.0000');
    expect($activity->maximale_teilnehmerzahl)->toBe(12);

    $sessions = Session::whereRaw('aktivitaet_id = ?', [\Ramsey\Uuid\Uuid::fromString($activity->id)->getBytes()])
        ->orderBy('beginn')
        ->get();

    expect($sessions)->toHaveCount(4);

    [$expectedDate] = getExpectedFirstSessionDate('wednesday');
    foreach ($sessions as $session) {
        expect($session->beginn->format('H:i'))->toBe('08:00');
        expect($session->ende->format('H:i'))->toBe('09:00');
        expect($session->beginn->format('Y-m-d'))->toBe($expectedDate->format('Y-m-d'));
        expect($session->ort)->toBe('Studio 1');

        $expectedDate = $expectedDate->copy()->addWeek();
    }
});

it('allows editing the generated activity before publishing', function (): void {
    $operation = new GenerateSessionsFromTemplate();
    $result = $operation->execute(new GenerateRequest($this->template->id));

    $activity = Activity::findById($result->unwrap()->activityId);
    expect($activity)->not->toBeNull();

    $activity->titel = 'Morgenyoga bearbeitet';
    $activity->save();

    $updated = Activity::findById($activity->id);
    expect($updated->titel)->toBe('Morgenyoga bearbeitet');
    expect($updated->status)->toBe(ActivityStatus::Draft);
    expect($updated->veroeffentlicht)->toBeFalse();
});

it('fails with invalid_template when the template has invalid values', function (): void {
    $this->template->dauer_minuten = 0;
    $this->template->save();

    $operation = new GenerateSessionsFromTemplate();
    $result = $operation->execute(new GenerateRequest($this->template->id));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.invalid');
});

it('fails with not_found when the template does not exist', function (): void {
    $operation = new GenerateSessionsFromTemplate();
    $result = $operation->execute(new GenerateRequest('018e1234-5678-7abc-8def-0123456789ab'));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('course_template.not_found');
});

/**
 * @return array{0: Carbon, 1: string}
 */
function getExpectedFirstSessionDate(string $weekday): array
{
    $dayMap = [
        'monday' => Carbon::MONDAY,
        'tuesday' => Carbon::TUESDAY,
        'wednesday' => Carbon::WEDNESDAY,
        'thursday' => Carbon::THURSDAY,
        'friday' => Carbon::FRIDAY,
        'saturday' => Carbon::SATURDAY,
        'sunday' => Carbon::SUNDAY,
    ];

    $target = $dayMap[$weekday] ?? Carbon::WEDNESDAY;
    $now = Carbon::now()->startOfDay();

    if ($now->dayOfWeekIso === $target) {
        return [$now, 'heute'];
    }

    return [$now->next($target), 'naechster Tag'];
}
