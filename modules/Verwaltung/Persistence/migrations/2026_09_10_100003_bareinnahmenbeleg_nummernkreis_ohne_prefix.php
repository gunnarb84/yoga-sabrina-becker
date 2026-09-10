<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Yoga\Platform\NumberSequence\Domain\NumberSequenceDefinition;

return new class () extends Migration {
    public function up(): void
    {
        // Belegnummern des Bareinnahmenbelegs folgen der Papiervorlage:
        // „2026-00012" statt „B-2026-00012". Das System ist noch nicht
        // produktiv; bestehende Belege existieren nur als Testdaten.
        NumberSequenceDefinition::where('code', 'B')->update(['prefix' => '', 'geaendert_am' => now()]);
    }

    public function down(): void
    {
        NumberSequenceDefinition::where('code', 'B')->update(['prefix' => 'B', 'geaendert_am' => now()]);
    }
};
