<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_warteliste', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->binary('anmeldung_id', 16);
            $table->unsignedInteger('rang');
            $table->timestamp('nachgerueckt_am')->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->unique('anmeldung_id', 'idx_warteliste_anmeldung_unique');
            $table->index(['anmeldung_id', 'rang'], 'idx_warteliste_rang');

            $table->foreign('anmeldung_id', 'fk_warteliste_anmeldung')
                ->references('id')
                ->on('verwaltung_anmeldungen')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_warteliste');
    }
};
