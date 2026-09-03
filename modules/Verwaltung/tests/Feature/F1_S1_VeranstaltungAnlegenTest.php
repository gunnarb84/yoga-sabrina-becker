<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F1 Veranstaltungen verwalten / S1 Veranstaltung anlegen
 *
 * Geprüfte Kriterien:
 * - CreateActivity legt eine Veranstaltung mit Status DRAFT an.
 * - CreateActivity verweigert die Anlage bei leerem Titel.
 * - CreateActivity verweigert die Anlage bei negativem Preis.
 * - CreateActivity verweigert die Anlage bei zu niedriger maximaler Teilnehmerzahl.
 */

use Yoga\Modules\Verwaltung\Application\Activity\CreateActivity\CreateActivity;
use Yoga\Modules\Verwaltung\Application\Activity\CreateActivity\Request as CreateActivityRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;

it('creates an activity with draft status', function (): void {
    $operation = new CreateActivity();
    $result = $operation->execute(new CreateActivityRequest(
        type: ActivityType::Course,
        title: 'Yoga-Kurs',
        shortDescription: 'Kurzbeschreibung',
        longDescription: null,
        price: '15.00',
        maxParticipants: 12,
        image: null,
    ));

    expect($result->isSuccess())->toBeTrue();

    $activity = Activity::findById($result->unwrap()->activityId);
    expect($activity)->not->toBeNull();
    expect($activity->status)->toBe(ActivityStatus::Draft);
    expect($activity->veroeffentlicht)->toBeFalse();
});

it('fails with title_empty when the title is empty', function (): void {
    $operation = new CreateActivity();
    $result = $operation->execute(new CreateActivityRequest(
        type: ActivityType::Course,
        title: '',
        shortDescription: null,
        longDescription: null,
        price: '15.00',
        maxParticipants: 12,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.title_empty');
});

it('fails with price_negative when the price is negative', function (): void {
    $operation = new CreateActivity();
    $result = $operation->execute(new CreateActivityRequest(
        type: ActivityType::Course,
        title: 'Yoga-Kurs',
        shortDescription: null,
        longDescription: null,
        price: '-1.00',
        maxParticipants: 12,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.price_negative');
});

it('fails with max_participants_too_low when max participants is below one', function (): void {
    $operation = new CreateActivity();
    $result = $operation->execute(new CreateActivityRequest(
        type: ActivityType::Course,
        title: 'Yoga-Kurs',
        shortDescription: null,
        longDescription: null,
        price: '15.00',
        maxParticipants: 0,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.max_participants_too_low');
});
