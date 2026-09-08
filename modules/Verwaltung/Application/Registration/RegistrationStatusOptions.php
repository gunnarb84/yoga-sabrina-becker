<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration;

use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationStatus;

final readonly class RegistrationStatusOptions
{
    /**
     * @return list<object{value: string, label: string}>
     */
    public function execute(): array
    {
        return array_map(
            fn (RegistrationStatus $status): object => (object) [
                'value' => $status->value,
                'label' => ucfirst($status->value),
            ],
            RegistrationStatus::cases(),
        );
    }
}
