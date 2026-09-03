<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_ausgehende_nachrichten', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('empfaenger');
            $table->string('betreff');
            $table->text('inhalt');
            $table->timestamp('versendet_am')->nullable();
            $table->string('status')->default('ausstehend');
            $table->binary('anmeldung_id', 16)->nullable();
            $table->string('fehlermeldung')->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('anmeldung_id', 'idx_ausgehende_nachrichten_anmeldung');
            $table->index(['status', 'versendet_am'], 'idx_ausgehende_nachrichten_status');

            $table->foreign('anmeldung_id', 'fk_ausgehende_nachrichten_anmeldung')
                ->references('id')
                ->on('verwaltung_anmeldungen')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_ausgehende_nachrichten');
    }
};
