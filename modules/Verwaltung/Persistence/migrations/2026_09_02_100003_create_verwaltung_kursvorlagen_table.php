<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_kursvorlagen', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('titel');
            $table->text('kurzbeschreibung')->nullable();
            $table->text('langbeschreibung')->nullable();
            $table->decimal('preis', 19, 4);
            $table->string('waehrung', 3)->default('EUR');
            $table->unsignedInteger('maximale_teilnehmerzahl');
            $table->string('wochentag');
            $table->time('startzeit');
            $table->unsignedInteger('dauer_minuten');
            $table->unsignedInteger('anzahl_termine');
            $table->string('ort')->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('titel', 'idx_kursvorlagen_titel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_kursvorlagen');
    }
};
