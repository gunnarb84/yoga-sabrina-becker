<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F1 Veranstaltungen verwalten / S3 Veranstaltung veröffentlichen
 *
 * Geprüfte Kriterien:
 * - PublishActivity setzt den Status auf PUBLISHED.
 * - PublishActivity scheitert mit activity.already_published, wenn der Status bereits PUBLISHED ist.
 * - UnpublishActivity setzt den Status auf DRAFT.
 * - UnpublishActivity scheitert mit activity.has_registrations, wenn bestätigte Anmeldungen
 *   oder Wartelisten-Einträge vorliegen.
 */

use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\PublishActivity;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\Request as PublishRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UnpublishActivity\Request as UnpublishRequest;
use Yoga\Modules\Verwaltung\Application\Activity\UnpublishActivity\UnpublishActivity;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\CreateParticipant as CreateParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\Request as CreateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Platform\NumberSequence\Application\NextNumber;

beforeEach(function (): void {
    $this->activity = TestFactory::createActivity(maxParticipants: 10);
});

it('publishes a draft activity', function (): void {
    $this->activity->status = ActivityStatus::Draft;
    $this->activity->veroeffentlicht = false;
    $this->activity->save();

    $publish = new PublishActivity();
    $result = $publish->execute(new PublishRequest($this->activity->id));

    expect($result->isSuccess())->toBeTrue();

    $updated = Activity::findById($this->activity->id);
    expect($updated->status)->toBe(ActivityStatus::Published);
    expect($updated->veroeffentlicht)->toBeTrue();
});

it('fails with already_published for a published activity', function (): void {
    $publish = new PublishActivity();
    $result = $publish->execute(new PublishRequest($this->activity->id));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.already_published');
});

it('unpublishes a published activity without registrations', function (): void {
    $unpublish = new UnpublishActivity();
    $result = $unpublish->execute(new UnpublishRequest($this->activity->id));

    expect($result->isSuccess())->toBeTrue();

    $updated = Activity::findById($this->activity->id);
    expect($updated->status)->toBe(ActivityStatus::Draft);
    expect($updated->veroeffentlicht)->toBeFalse();
});

it('fails to unpublish when registrations exist', function (): void {
    $participant = (new CreateParticipantOperation())->execute(new CreateParticipantRequest(
        email: 'teilnehmer@example.com',
        firstName: 'Max',
        lastName: 'Mustermann',
        addressLine1: null,
        addressLine2: null,
        postalCode: null,
        city: null,
        phone: null,
        dateOfBirth: null,
        healthNotes: null,
    ));

    $register = new RegisterParticipant(app(NextNumber::class));
    $register->execute(new RegisterRequest(
        activityId: $this->activity->id,
        participantId: $participant->unwrap()->participantId,
        paymentMethod: 'bar',
    ));

    $unpublish = new UnpublishActivity();
    $result = $unpublish->execute(new UnpublishRequest($this->activity->id));

    expect($result->isFailure())->toBeTrue();
    expect($result->error()['code'])->toBe('activity.has_registrations');
});
