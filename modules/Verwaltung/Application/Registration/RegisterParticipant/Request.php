<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration\RegisterParticipant;

use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;

final readonly class Request
{
    public function __construct(
        public string $activityId,
        public string $participantId,
        public RegistrationPaymentMethod $paymentMethod,
    ) {
    }
}
