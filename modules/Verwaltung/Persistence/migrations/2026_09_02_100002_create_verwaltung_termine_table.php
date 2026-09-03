<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_termine', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->binary('aktivitaet_id', 16);
            $table->timestamp('beginn');
            $table->timestamp('ende');
            $table->string('ort')->nullable();
            $table->text('hinweis')->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('aktivitaet_id', 'idx_termine_aktivitaet');
            $table->index('beginn', 'idx_termine_beginn');

            $table->foreign('aktivitaet_id', 'fk_termine_aktivitaet')
                ->references('id')
                ->on('verwaltung_aktivitaeten')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_termine');
    }
};
