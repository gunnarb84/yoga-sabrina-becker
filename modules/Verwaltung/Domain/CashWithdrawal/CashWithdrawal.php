<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\CashWithdrawal;

use Yoga\Modules\Verwaltung\Domain\Support\BaseModel;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property string $betrag
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $datum
 * @property string|null $fremdbelegnummer
 * @property string $id
 * @property int $version
 * @property string $zweck
 */
class CashWithdrawal extends BaseModel
{
    protected $table = 'verwaltung_barentnahmen';

    protected $guarded = [];

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'datum' => 'date',
        'betrag' => 'decimal:4',
        'version' => 'int',
    ];
}
