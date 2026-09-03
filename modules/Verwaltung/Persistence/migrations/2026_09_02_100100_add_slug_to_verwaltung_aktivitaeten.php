<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verwaltung_aktivitaeten', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('titel');
            $table->unique('slug', 'idx_aktivitaeten_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('verwaltung_aktivitaeten', function (Blueprint $table): void {
            $table->dropUnique('idx_aktivitaeten_slug_unique');
            $table->dropColumn('slug');
        });
    }
};
