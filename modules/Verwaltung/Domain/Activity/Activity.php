<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Registration\Registration;
use Yoga\Modules\Verwaltung\Domain\Session\Session;
use Yoga\Modules\Verwaltung\Domain\Support\EntityLifecycle;
use Yoga\Platform\Identity\UuidCast;

class Activity extends Model
{
    use EntityLifecycle;

    protected $table = 'verwaltung_aktivitaeten';

    protected $guarded = [];

    public static function findById(string $id): ?self
    {
        return self::query()->whereRaw('id = ?', [Uuid::fromString($id)->getBytes()])->first();
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::query()->where('slug', $slug)->first();
    }

    protected $casts = [
        'id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'typ' => ActivityType::class,
        'status' => ActivityStatus::class,
        'preis' => 'decimal:4',
        'veroeffentlicht' => 'boolean',
        'maximale_teilnehmerzahl' => 'int',
        'version' => 'int',
    ];

    /**
     * @return HasMany<Session, $this>
     */
    public function termine(): HasMany
    {
        return $this->hasMany(Session::class, 'aktivitaet_id');
    }

    /**
     * @return HasMany<Registration, $this>
     */
    public function anmeldungen(): HasMany
    {
        return $this->hasMany(Registration::class, 'aktivitaet_id');
    }

    public function publish(): void
    {
        $this->status = ActivityStatus::Published;
        $this->veroeffentlicht = true;
    }

    public function complete(): void
    {
        $this->status = ActivityStatus::Completed;
    }

    public function cancel(): void
    {
        $this->status = ActivityStatus::Cancelled;
    }
}
