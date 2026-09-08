<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Seeders;

use Illuminate\Database\Seeder;
use Yoga\Modules\Verwaltung\Application\Activity\CreateActivity\CreateActivity as CreateActivityOperation;
use Yoga\Modules\Verwaltung\Application\Activity\CreateActivity\Request as CreateActivityRequest;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\PublishActivity as PublishActivityOperation;
use Yoga\Modules\Verwaltung\Application\Activity\PublishActivity\Request as PublishActivityRequest;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\CreateParticipant as CreateParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant\Request as CreateParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\RegisterParticipant as RegisterParticipantOperation;
use Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant\Request as RegisterParticipantRequest;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\CreateSession as CreateSessionOperation;
use Yoga\Modules\Verwaltung\Application\Session\CreateSession\Request as CreateSessionRequest;
use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;

final class DemoSeeder extends Seeder
{
    public function run(
        CreateActivityOperation $createActivity,
        CreateSessionOperation $createSession,
        PublishActivityOperation $publishActivity,
        CreateParticipantOperation $createParticipant,
        RegisterParticipantOperation $registerParticipant,
    ): void {
        $createResult = $createActivity->execute(new CreateActivityRequest(
            type: ActivityType::Workshop->value,
            title: 'Yoga-Workshop: Rücken fit im Alltag',
            shortDescription: 'Ein praxisnaher Workshop mit Fokus auf Rückenmobilisation und Entspannung.',
            longDescription: 'In diesem vierstündigen Workshop erlernen Sie gezielte Übungen zur Stärkung der Rückenmuskulatur und zur Verbesserung der Körperhaltung im Alltag. Zwischen den Einheiten gibt es Pausen mit Tee und Snacks.',
            price: '75.00',
            maxParticipants: 12,
            image: null,
        ));

        $activityId = $createResult->unwrap()->activityId;

        $sessionResult = $createSession->execute(new CreateSessionRequest(
            activityId: $activityId,
            startsAt: '2026-10-10 10:00:00',
            endsAt: '2026-10-10 14:00:00',
            location: 'Yoga-Studio Sabrina Becker, Musterstraße 1, 12345 Musterstadt',
            note: 'Bitte bequeme Kleidung mitbringen.',
        ));

        if ($sessionResult->isFailure()) {
            throw new \RuntimeException('Session creation failed: ' . json_encode($sessionResult->error()));
        }

        $publishResult = $publishActivity->execute(new PublishActivityRequest($activityId));

        if ($publishResult->isFailure()) {
            throw new \RuntimeException('Publish failed: ' . json_encode($publishResult->error()));
        }

        $participantResult = $createParticipant->execute(new CreateParticipantRequest(
            email: 'max@example.com',
            firstName: 'Max',
            lastName: 'Mustermann',
            addressLine1: 'Musterstraße 5',
            addressLine2: null,
            postalCode: '12345',
            city: 'Musterstadt',
            phone: '0123456789',
            dateOfBirth: '1985-06-15',
            healthNotes: null,
        ));

        if ($participantResult->isFailure()) {
            throw new \RuntimeException('Participant creation failed: ' . json_encode($participantResult->error()));
        }

        $participantId = $participantResult->unwrap()->participantId;

        $registrationResult = $registerParticipant->execute(new RegisterParticipantRequest(
            activityId: $activityId,
            participantId: $participantId,
            paymentMethod: 'bar',
        ));

        if ($registrationResult->isFailure()) {
            throw new \RuntimeException('Registration failed: ' . json_encode($registrationResult->error()));
        }
    }
}
