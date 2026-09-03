<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_anmeldungen', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->binary('aktivitaet_id', 16);
            $table->binary('teilnehmer_id', 16);
            $table->timestamp('angemeldet_am')->useCurrent();
            $table->string('status')->default('bestaetigt');
            $table->string('zahlungsart')->default('bar');
            $table->binary('zahlung_id', 16)->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index(['aktivitaet_id', 'status'], 'idx_anmeldungen_aktivitaet_status');
            $table->index('teilnehmer_id', 'idx_anmeldungen_teilnehmer');
            $table->index('zahlung_id', 'idx_anmeldungen_zahlung');

            $table->foreign('aktivitaet_id', 'fk_anmeldungen_aktivitaet')
                ->references('id')
                ->on('verwaltung_aktivitaeten')
                ->onDelete('cascade');

            $table->foreign('teilnehmer_id', 'fk_anmeldungen_teilnehmer')
                ->references('id')
                ->on('verwaltung_teilnehmer')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_anmeldungen');
    }
};
