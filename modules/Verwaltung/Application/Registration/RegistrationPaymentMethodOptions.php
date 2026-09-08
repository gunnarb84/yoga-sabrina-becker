<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Registration;

use Yoga\Modules\Verwaltung\Domain\Registration\RegistrationPaymentMethod;

final readonly class RegistrationPaymentMethodOptions
{
    /**
     * @return list<object{value: string, label: string}>
     */
    public function execute(): array
    {
        return array_map(
            fn (RegistrationPaymentMethod $method): object => (object) [
                'value' => $method->value,
                'label' => ucfirst($method->value),
            ],
            RegistrationPaymentMethod::cases(),
        );
    }
}
