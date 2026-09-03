<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_bareinnahmenbelege', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('nummer')->unique();
            $table->binary('zahlung_id', 16);
            $table->timestamp('ausgestellt_am')->useCurrent();
            $table->string('empfaenger');
            $table->decimal('betrag', 19, 4);
            $table->string('waehrung', 3)->default('EUR');
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('zahlung_id', 'idx_bareinnahmenbelege_zahlung');

            $table->foreign('zahlung_id', 'fk_bareinnahmenbelege_zahlung')
                ->references('id')
                ->on('verwaltung_zahlungen')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_bareinnahmenbelege');
    }
};
