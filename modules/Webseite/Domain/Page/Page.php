<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Domain\Page;

use Illuminate\Database\Eloquent\Model;
use Yoga\Platform\Identity\UuidCast;
use Yoga\Platform\Shared\Domain\PlatformEntityLifecycle;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property string $inhalt
 * @property string|null $meta_beschreibung
 * @property string $slug
 * @property string $titel
 * @property bool $veroeffentlicht
 * @property int $version
 */
class Page extends Model
{
    use PlatformEntityLifecycle;

    protected $table = 'webseite_pages';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public const CREATED_AT = 'angelegt_am';

    public const UPDATED_AT = 'geaendert_am';

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'veroeffentlicht' => 'boolean',
        'version' => 'int',
    ];

    public static function findBySlug(string $slug): ?self
    {
        return self::query()->where('slug', $slug)->first();
    }
}
