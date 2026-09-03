<?php

declare(strict_types=1);

/**
 * Story: E1 Öffentliche Webseite / F2 Veranstaltungsdarstellung
 *
 * Geprüfte Kriterien:
 * - Übersicht zeigt nur zukünftige, veröffentlichte Veranstaltungen.
 * - Übersicht lässt sich nach Typ filtern.
 * - Ausgebuchte Veranstaltungen zeigen Wartelistenhinweis.
 * - Detailseite zeigt freie Plätze, Warteliste, Termine und Anmeldebutton.
 */

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Application\Activity\PublicDetail\ActivityDetailQuery;
use Yoga\Modules\Verwaltung\Application\Activity\PublicOverview\ActivityOverviewQuery;
use Yoga\Modules\Verwaltung\Application\Activity\PublicPublishedActivities\PublishedActivitiesQuery;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;
use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;

beforeEach(function (): void {
    $this->future = Carbon::now()->addDays(7);
    $this->past = Carbon::now()->subDays(7);
});

it('lists only future published activities on the homepage', function (): void {
    $published = TestFactory::createActivity();
    $published->typ = ActivityType::Workshop->value;
    $published->save();
    TestFactory::createSession($published, $this->future);

    $unpublished = new Activity([
        'typ' => ActivityType::Workshop->value,
        'titel' => 'Unveröffentlicht',
        'slug' => 'unveroeffentlicht-' . uniqid(),
        'kurzbeschreibung' => null,
        'langbeschreibung' => null,
        'preis' => 20,
        'waehrung' => 'EUR',
        'maximale_teilnehmerzahl' => 10,
        'veroeffentlicht' => false,
        'status' => ActivityStatus::Draft->value,
    ]);
    $unpublished->save();
    TestFactory::createSession($unpublished, $this->future);

    $pastActivity = TestFactory::createActivity();
    TestFactory::createSession($pastActivity, $this->past);

    $query = new PublishedActivitiesQuery();
    $activities = $query->execute();

    expect($activities)->toHaveCount(1);
    expect($activities[0]->slug)->toBe($published->slug);
    expect($activities[0]->naechster_termin)->not->toBeNull();
});

it('filters activities by type in the overview', function (): void {
    $course = TestFactory::createActivity();
    $course->typ = ActivityType::Course->value;
    $course->save();
    TestFactory::createSession($course, $this->future);

    $event = TestFactory::createActivity();
    $event->typ = ActivityType::Event->value;
    $event->save();
    TestFactory::createSession($event, $this->future);

    $query = new ActivityOverviewQuery();
    $all = $query->execute();
    expect($all)->toHaveCount(2);

    $courses = $query->execute(ActivityType::Course->value);
    expect($courses)->toHaveCount(1);
    expect($courses[0]->typ)->toBe(ActivityType::Course->value);
});

it('shows waiting list hint for fully booked activities', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 1);
    TestFactory::createSession($activity, $this->future);

    $confirmed = TestFactory::createParticipant(email: 'confirmed@example.com');
    $waiting = TestFactory::createParticipant(email: 'waiting@example.com');

    new Registration([
        'aktivitaet_id' => $activity->id,
        'teilnehmer_id' => $confirmed->id,
        'angemeldet_am' => now(),
        'status' => RegistrationStatus::Confirmed->value,
        'zahlungsart' => RegistrationPaymentMethod::Cash->value,
        'zahlungsstatus' => 'offen',
    ])->save();

    new Registration([
        'aktivitaet_id' => $activity->id,
        'teilnehmer_id' => $waiting->id,
        'angemeldet_am' => now(),
        'status' => RegistrationStatus::WaitingList->value,
        'zahlungsart' => RegistrationPaymentMethod::Cash->value,
        'zahlungsstatus' => 'offen',
    ])->save();

    $query = new ActivityOverviewQuery();
    $activities = $query->execute();

    expect($activities)->toHaveCount(1);
    expect($activities[0]->ausgebucht)->toBeTrue();
    expect($activities[0]->warteliste_anzahl)->toBe(1);
    expect($activities[0]->freie_plaetze)->toBe(0);
});

it('shows activity details by slug with free seats and sessions', function (): void {
    $activity = TestFactory::createActivity(maxParticipants: 3);
    TestFactory::createSession($activity, $this->future, ort: 'Studio A');

    $participant = TestFactory::createParticipant(email: 'detail@example.com');
    new Registration([
        'aktivitaet_id' => $activity->id,
        'teilnehmer_id' => $participant->id,
        'angemeldet_am' => now(),
        'status' => RegistrationStatus::Confirmed->value,
        'zahlungsart' => RegistrationPaymentMethod::Cash->value,
        'zahlungsstatus' => 'offen',
    ])->save();

    $query = new ActivityDetailQuery();
    $result = $query->execute($activity->slug);

    expect($result->isSuccess())->toBeTrue();

    $detail = $result->unwrap();
    expect($detail->slug)->toBe($activity->slug);
    expect($detail->freie_plaetze)->toBe(2);
    expect($detail->ausgebucht)->toBeFalse();
    expect($detail->buchbar)->toBeTrue();
    expect($detail->termine)->toHaveCount(1);
});

it('marks activity as not bookable when it has no future sessions', function (): void {
    $activity = TestFactory::createActivity();
    TestFactory::createSession($activity, $this->past);

    $query = new ActivityDetailQuery();
    $result = $query->execute($activity->slug);

    expect($result->isSuccess())->toBeTrue();
    expect($result->unwrap()->buchbar)->toBeFalse();
    expect($result->unwrap()->termine)->toBeEmpty();
});
