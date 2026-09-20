<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verwaltung_teilnehmer', function (Blueprint $table): void {
            $table->boolean('foto_einwilligung')->default(false);
            $table->timestamp('foto_einwilligung_am')->nullable();
            $table->boolean('video_einwilligung')->default(false);
            $table->timestamp('video_einwilligung_am')->nullable();
            $table->timestamp('widerruf_am')->nullable();
            $table->text('widerrufsvermerk')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('verwaltung_teilnehmer', function (Blueprint $table): void {
            $table->dropColumn([
                'foto_einwilligung',
                'foto_einwilligung_am',
                'video_einwilligung',
                'video_einwilligung_am',
                'widerruf_am',
                'widerrufsvermerk',
            ]);
        });
    }
};
