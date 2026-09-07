<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Registration;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Ramsey\Uuid\Uuid;
use Yoga\Modules\Verwaltung\Domain\Activity\Activity;
use Yoga\Modules\Verwaltung\Domain\Participant\Participant;
use Yoga\Modules\Verwaltung\Domain\Payment\Payment;
use Yoga\Modules\Verwaltung\Domain\Support\BaseModel;
use Yoga\Modules\Verwaltung\Domain\WaitingList\WaitingList;
use Yoga\Platform\Identity\UuidCast;

/**
 * @property string $aktivitaet_id
 * @property \Carbon\Carbon $angelegt_am
 * @property string|null $angelegt_von
 * @property \Carbon\Carbon $angemeldet_am
 * @property \Carbon\Carbon|null $geaendert_am
 * @property string|null $geaendert_von
 * @property string $id
 * @property RegistrationStatus $status
 * @property string $teilnehmer_id
 * @property int $version
 * @property string|null $zahlung_id
 * @property RegistrationPaymentMethod $zahlungsart
 * @property RegistrationPaymentStatus $zahlungsstatus
 */
class Registration extends BaseModel
{
    protected $table = 'verwaltung_anmeldungen';

    protected $guarded = [];

    public static function findById(string $id): ?self
    {
        return self::query()->whereRaw('id = ?', [Uuid::fromString($id)->getBytes()])->first();
    }

    protected $casts = [
        'id' => UuidCast::class,
        'aktivitaet_id' => UuidCast::class,
        'teilnehmer_id' => UuidCast::class,
        'zahlung_id' => UuidCast::class,
        'angelegt_von' => UuidCast::class,
        'geaendert_von' => UuidCast::class,
        'angemeldet_am' => 'datetime',
        'status' => RegistrationStatus::class,
        'zahlungsart' => RegistrationPaymentMethod::class,
        'zahlungsstatus' => RegistrationPaymentStatus::class,
        'version' => 'int',
    ];

    /**
     * @return BelongsTo<Activity, $this>
     */
    public function aktivitaet(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'aktivitaet_id');
    }

    /**
     * @return BelongsTo<Participant, $this>
     */
    public function teilnehmer(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'teilnehmer_id');
    }

    /**
     * @return HasOne<WaitingList, $this>
     */
    public function wartelistenEintrag(): HasOne
    {
        return $this->hasOne(WaitingList::class, 'anmeldung_id');
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function zahlungen(): HasMany
    {
        return $this->hasMany(Payment::class, 'anmeldung_id');
    }

    public function putOnWaitingList(int $rank): void
    {
        $this->status = RegistrationStatus::WaitingList;
    }

    public function confirm(): void
    {
        $this->status = RegistrationStatus::Confirmed;
    }

    public function cancel(): void
    {
        $this->status = RegistrationStatus::Cancelled;
    }

    public function markAsPaid(): void
    {
        $this->zahlungsstatus = RegistrationPaymentStatus::Paid;
    }
}
