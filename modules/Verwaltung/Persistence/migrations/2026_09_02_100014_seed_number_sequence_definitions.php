<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Yoga\Platform\NumberSequence\Domain\NumberSequenceDefinition;

return new class () extends Migration {
    public function up(): void
    {
        $definitions = [
            [
                'code' => 'B',
                'bezeichnung' => 'Bareinnahmenbeleg',
                'prefix' => 'B',
            ],
            [
                'code' => 'R',
                'bezeichnung' => 'Rechnung',
                'prefix' => 'R',
            ],
            [
                'code' => 'G',
                'bezeichnung' => 'Gutschrift',
                'prefix' => 'G',
            ],
            [
                'code' => 'RB',
                'bezeichnung' => 'Rückgabebestätigung',
                'prefix' => 'RB',
            ],
        ];

        foreach ($definitions as $definition) {
            if (NumberSequenceDefinition::where('code', $definition['code'])->exists()) {
                continue;
            }

            $model = new NumberSequenceDefinition([
                'code' => $definition['code'],
                'bezeichnung' => $definition['bezeichnung'],
                'prefix' => $definition['prefix'],
                'jahr_stellen' => 4,
                'start_nummer' => 1,
                'laenge_nummer' => 5,
                'geaendert_am' => now(),
            ]);

            $model->save();
        }
    }

    public function down(): void
    {
        NumberSequenceDefinition::whereIn('code', ['B', 'R', 'G', 'RB'])->delete();
    }
};
