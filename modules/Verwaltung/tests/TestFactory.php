<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Tests;

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\CreateParticipant as CreateParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\Request as CreateParticipantRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityStatus;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Session\Session;

final class TestFactory
{
    public static function createActivity(int $maxParticipants = 12): Activity
    {
        $activity = new Activity([
            'typ' => ActivityType::Workshop->value,
            'titel' => 'Test-Workshop',
            'slug' => 'test-workshop-' . uniqid(),
            'kurzbeschreibung' => 'Kurzbeschreibung',
            'langbeschreibung' => 'Langbeschreibung',
            'preis' => 45.00,
            'waehrung' => 'EUR',
            'maximale_teilnehmerzahl' => $maxParticipants,
            'veroeffentlicht' => true,
            'status' => ActivityStatus::Published->value,
        ]);
        $activity->save();

        return $activity;
    }

    public static function createParticipant(string $email, string $vorname = 'Max', string $nachname = 'Mustermann'): Participant
    {
        $operation = new CreateParticipantOperation();
        $result = $operation->execute(new CreateParticipantRequest(
            email: $email,
            firstName: $vorname,
            lastName: $nachname,
            addressLine1: null,
            addressLine2: null,
            postalCode: null,
            city: null,
            phone: null,
            dateOfBirth: null,
            healthNotes: null,
        ));

        if ($result->isFailure()) {
            throw new \RuntimeException('Failed to create test participant: '.$result->error()['code']);
        }

        $participant = Participant::findById($result->value()->participantId);
        if ($participant === null) {
            throw new \RuntimeException('Created participant not found');
        }

        return $participant;
    }

    public static function createSession(Activity $activity, Carbon $beginn, ?Carbon $ende = null, string $ort = ''): Session
    {
        $session = new Session([
            'aktivitaet_id' => $activity->getAttribute('id'),
            'beginn' => $beginn,
            'ende' => $ende ?? $beginn->clone()->addHour(),
            'ort' => $ort,
        ]);
        $session->save();

        return $session;
    }
}
