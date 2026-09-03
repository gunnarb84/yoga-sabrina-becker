<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F1 Veranstaltungen verwalten / S2 Veranstaltung bearbeiten
 *
 * Geprüfte Kriterien:
 * - UpdateActivity speichert Änderungen an einer bestehenden Veranstaltung.
 * - UpdateActivity scheitert mit activity.not_found, wenn die Veranstaltung nicht existiert.
 * - UpdateActivity scheitert mit activity.already_completed, wenn der Status COMPLETED ist.
 * - UpdateActivity scheitert mit activity.already_cancelled, wenn der Status CANCELLED ist.
 */

use Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity\Request as UpdateActivityRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UpdateActivity\UpdateActivity;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Tests\TestFactory;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 10);
});

it('updates the activity fields', function (): void {
    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: $this->activity->id,
        type: ActivityType::Event,
        title: 'Bearbeiteter Titel',
        shortDescription: 'Neue Kurzbeschreibung',
        longDescription: 'Neue Langbeschreibung',
        price: '99.99',
        maxParticipants: 20,
        image: null,
    ));

    expect($result->isSuccess())->toBeTrue();

    $updated = Activity::findById($this->activity->id);
    expect($updated)->not->toBeNull();
    expect($updated->typ)->toBe(ActivityType::Event);
    expect($updated->titel)->toBe('Bearbeiteter Titel');
    expect($updated->kurzbeschreibung)->toBe('Neue Kurzbeschreibung');
    expect($updated->langbeschreibung)->toBe('Neue Langbeschreibung');
    expect($updated->preis)->toBe('99.9900');
    expect($updated->maximale_teilnehmerzahl)->toBe(20);
});

it('fails with not_found for a non-existing activity', function (): void {
    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: '018e1234-5678-7abc-8def-0123456789ab',
        type: ActivityType::Workshop,
        title: 'Titel',
        shortDescription: null,
        longDescription: null,
        price: '10.00',
        maxParticipants: 5,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.not_found');
});

it('fails with already_completed for a completed activity', function (): void {
    $this->activity->status = ActivityStatus::Completed;
    $this->activity->save();

    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: $this->activity->id,
        type: ActivityType::Workshop,
        title: 'Titel',
        shortDescription: null,
        longDescription: null,
        price: '10.00',
        maxParticipants: 5,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.already_completed');
});

it('fails with already_cancelled for a cancelled activity', function (): void {
    $this->activity->status = ActivityStatus::Cancelled;
    $this->activity->save();

    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: $this->activity->id,
        type: ActivityType::Workshop,
        title: 'Titel',
        shortDescription: null,
        longDescription: null,
        price: '10.00',
        maxParticipants: 5,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.already_cancelled');
});

it('fails with title_empty when the title is empty', function (): void {
    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: $this->activity->id,
        type: ActivityType::Workshop,
        title: '',
        shortDescription: null,
        longDescription: null,
        price: '10.00',
        maxParticipants: 5,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.title_empty');
});

it('fails with price_negative when the price is negative', function (): void {
    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: $this->activity->id,
        type: ActivityType::Workshop,
        title: 'Titel',
        shortDescription: null,
        longDescription: null,
        price: '-1.00',
        maxParticipants: 5,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.price_negative');
});

it('fails with max_participants_too_low when max participants is below one', function (): void {
    $operation = new UpdateActivity();
    $result = $operation->execute(new UpdateActivityRequest(
        activityId: $this->activity->id,
        type: ActivityType::Workshop,
        title: 'Titel',
        shortDescription: null,
        longDescription: null,
        price: '10.00',
        maxParticipants: 0,
        image: null,
    ));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.max_participants_too_low');
});
