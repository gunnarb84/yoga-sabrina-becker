<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Domain\Support;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use EntityLifecycle;

    public $incrementing = false;

    public $timestamps = true;

    protected $keyType = 'string';

    public const CREATED_AT = 'angelegt_am';

    public const UPDATED_AT = 'geaendert_am';
}
