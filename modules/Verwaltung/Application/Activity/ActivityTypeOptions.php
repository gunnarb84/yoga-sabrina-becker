<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Application\Activity;

use Yoga\Modules\Verwaltung\Domain\Activity\ActivityType;

final readonly class ActivityTypeOptions
{
    /**
     * @return list<object{value: string, label: string}>
     */
    public function execute(): array
    {
        return array_map(
            fn (ActivityType $type): object => (object) [
                'value' => $type->value,
                'label' => ucfirst($type->value),
            ],
            ActivityType::cases(),
        );
    }
}
