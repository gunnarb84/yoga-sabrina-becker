<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_rueckgabebestaetigungen', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('nummer')->unique();
            $table->binary('bareinnahmenbeleg_id', 16);
            $table->timestamp('ausgestellt_am')->useCurrent();
            $table->string('empfaenger');
            $table->decimal('betrag', 19, 4);
            $table->string('waehrung', 3)->default('EUR');
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('bareinnahmenbeleg_id', 'idx_rueckgabebestaetigungen_bareinnahmenbeleg');

            $table->foreign('bareinnahmenbeleg_id', 'fk_rueckgabebestaetigungen_bareinnahmenbeleg')
                ->references('id')
                ->on('verwaltung_bareinnahmenbelege')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_rueckgabebestaetigungen');
    }
};
