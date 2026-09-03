<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Domain\Navigation;

use Illuminate\Database\Eloquent\Model;
use Yoga\Platform\Identity\UuidCast;
use Yoga\Platform\Shared\Domain\PlatformEntityLifecycle;

class NavigationItem extends Model
{
    use PlatformEntityLifecycle;

    protected $table = 'webseite_navigation_items';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public const CREATED_AT = 'angelegt_am';

    public const UPDATED_AT = 'geaendert_am';

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'extern' => 'boolean',
        'aktiv' => 'boolean',
        'sortierung' => 'int',
        'version' => 'int',
    ];
}
