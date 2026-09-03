<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_aktivitaeten', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('typ');
            $table->string('titel');
            $table->text('kurzbeschreibung')->nullable();
            $table->text('langbeschreibung')->nullable();
            $table->decimal('preis', 19, 4);
            $table->string('waehrung', 3)->default('EUR');
            $table->unsignedInteger('maximale_teilnehmerzahl');
            $table->boolean('veroeffentlicht')->default(false);
            $table->string('status')->default('entwurf');
            $table->text('bild')->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index(['typ', 'status', 'veroeffentlicht'], 'idx_aktivitaeten_liste');
            $table->index('titel', 'idx_aktivitaeten_titel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_aktivitaeten');
    }
};
