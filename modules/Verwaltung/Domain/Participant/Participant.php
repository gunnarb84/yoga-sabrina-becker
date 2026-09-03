<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Participant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class Participant extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_teilnehmer';

    protected $guarded = [];

    public static function findById(string $id): ?self
    {
        return self::query()->whereRaw('id = ?', [Uuid::fromString($id)->getBytes()])->first();
    }

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'geburtsdatum' => 'date',
        'gesundheitsinformationen_einwilligung' => 'boolean',
        'version' => 'int',
    ];

    /**
     * @return HasMany<Registration, $this>
     */
    public function anmeldungen(): HasMany
    {
        return $this->hasMany(Registration::class, 'teilnehmer_id');
    }
}
