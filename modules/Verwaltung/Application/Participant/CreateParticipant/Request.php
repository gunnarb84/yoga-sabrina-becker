<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Participant\CreateParticipant;

final readonly class Request
{
    public function __construct(
        public ?string $email,
        public string $firstName,
        public string $lastName,
        public ?string $addressLine1,
        public ?string $addressLine2,
        public ?string $postalCode,
        public ?string $city,
        public ?string $phone,
        public ?string $dateOfBirth,
        public ?string $healthNotes,
    ) {
    }
}
