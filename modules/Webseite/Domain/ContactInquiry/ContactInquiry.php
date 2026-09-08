<?php

declare(strict_types=1);

namespace Yoga\Modules\Webseite\Domain\ContactInquiry;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Yoga\Platform\Identity\UuidCast;
use Yoga\Platform\Shared\Domain\PlatformEntityLifecycle;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property string $anlass
 * @property string $email
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property string $name
 * @property string $nachricht
 * @property string|null $notiz
 * @property \Carbon\Carbon $empfangen_am
 * @property string|null $telefon
 * @property ContactInquiryStatus $status
 * @property int $version
 */
class ContactInquiry extends Model
{
    use PlatformEntityLifecycle;

    protected $table = 'webseite_kontaktanfragen';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public const CREATED_AT = 'angelegt_am';

    public const UPDATED_AT = 'geaendert_am';

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'empfangen_am' => 'datetime',
        'status' => ContactInquiryStatus::class,
        'version' => 'int',
    ];

    public static function findById(string $id): ?self
    {
        $bytes = Uuid::fromString($id)->getBytes();

        return self::query()->whereRaw('id = ?', [$bytes])->first();
    }

    public function aendere(ContactInquiryStatus $status, ?string $notiz): void
    {
        $this->status = $status;
        $this->notiz = $notiz;
    }
}
