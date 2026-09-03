<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F1 Veranstaltungen verwalten / S4 Termine erfassen
 *
 * Geprüfte Kriterien:
 * - CreateSession legt einen Termin mit Beginn, Ende, Ort und Hinweis an.
 * - endsAt muss nach startsAt liegen.
 * - UpdateSession ändert einen Termin.
 * - DeleteSession entfernt einen Termin.
 */

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\CreateSession;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\Request as CreateSessionRequest;
use Yoga\Modules\Verwaltung\Application\Session\DeleteSession\DeleteSession;
use Yoga\Modules\Verwaltung\Application\Session\DeleteSession\Request as DeleteSessionRequest;
use Yoga\Modules\Verwaltung\Application\Session\SessionsByActivity\SessionsByActivityQuery;
use Yoga\Modules\Verwaltung\Application\Session\UpdateSession\Request as UpdateSessionRequest;
use Yoga\Modules\Verwaltung\Application\Session\UpdateSession\UpdateSession;
use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Modules\Verwaltung\Tests\TestFactory;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
    $this->activity = TestFactory::createActivity();
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('creates a session with startsAt, endsAt, location and note', function (): void {
    $operation = new CreateSession();
    $result = $operation->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '2026-10-05 10:00:00',
        endsAt: '2026-10-05 11:30:00',
        location: 'Yogaraum',
        note: 'Bitte Matte mitbringen',
    ));

    expect($result->isSuccess())->toBeTrue();

    $session = Session::findById($result->unwrap()->sessionId);
    expect($session)->not->toBeNull();
    expect($session->beginn->format('Y-m-d H:i:s'))->toBe('2026-10-05 10:00:00');
    expect($session->ende->format('Y-m-d H:i:s'))->toBe('2026-10-05 11:30:00');
    expect($session->ort)->toBe('Yogaraum');
    expect($session->hinweis)->toBe('Bitte Matte mitbringen');
});

it('fails when startsAt is empty', function (): void {
    $operation = new CreateSession();
    $result = $operation->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '',
        endsAt: '2026-10-05 11:30:00',
        location: null,
        note: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('session.starts_at_empty');
});

it('fails when endsAt is empty', function (): void {
    $operation = new CreateSession();
    $result = $operation->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '2026-10-05 10:00:00',
        endsAt: '',
        location: null,
        note: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('session.ends_at_empty');
});

it('fails when endsAt is not after startsAt', function (): void {
    $operation = new CreateSession();
    $result = $operation->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '2026-10-05 11:30:00',
        endsAt: '2026-10-05 10:00:00',
        location: null,
        note: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('session.ends_at_not_after_starts_at');
});

it('updates a session', function (): void {
    $create = new CreateSession();
    $created = $create->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '2026-10-05 10:00:00',
        endsAt: '2026-10-05 11:30:00',
        location: 'Yogaraum',
        note: null,
    ));

    $update = new UpdateSession();
    $result = $update->execute(new UpdateSessionRequest(
        sessionId: $created->unwrap()->sessionId,
        startsAt: '2026-10-06 09:00:00',
        endsAt: '2026-10-06 10:30:00',
        location: 'Online',
        note: 'Link folgt',
    ));

    expect($result->isSuccess())->toBeTrue();

    $session = Session::findById($created->unwrap()->sessionId);
    expect($session)->not->toBeNull();
    expect($session->beginn->format('Y-m-d H:i:s'))->toBe('2026-10-06 09:00:00');
    expect($session->ende->format('Y-m-d H:i:s'))->toBe('2026-10-06 10:30:00');
    expect($session->ort)->toBe('Online');
    expect($session->hinweis)->toBe('Link folgt');
});

it('deletes a session', function (): void {
    $create = new CreateSession();
    $created = $create->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '2026-10-05 10:00:00',
        endsAt: '2026-10-05 11:30:00',
        location: null,
        note: null,
    ));

    $delete = new DeleteSession();
    $result = $delete->execute(new DeleteSessionRequest($created->unwrap()->sessionId));

    expect($result->isSuccess())->toBeTrue();
    expect(Session::findById($created->unwrap()->sessionId))->toBeNull();
});

it('marks past sessions in the query result', function (): void {
    $create = new CreateSession();
    $created = $create->execute(new CreateSessionRequest(
        activityId: $this->activity->id,
        startsAt: '2026-09-01 10:00:00',
        endsAt: '2026-09-01 11:30:00',
        location: null,
        note: null,
    ));

    $query = new SessionsByActivityQuery();
    $sessions = $query->execute($this->activity->id);

    expect($sessions)->toHaveCount(1);
    expect($sessions[0]->id)->toBe($created->unwrap()->sessionId);
    expect($sessions[0]->vergangen)->toBeTrue();
});
