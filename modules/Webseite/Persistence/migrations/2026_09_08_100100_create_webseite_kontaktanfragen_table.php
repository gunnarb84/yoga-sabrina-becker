<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('webseite_kontaktanfragen', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('name', 200);
            $table->string('email', 255);
            $table->string('telefon', 50)->nullable();
            $table->string('anlass', 200)->nullable();
            $table->text('nachricht');
            $table->string('status', 50)->default('neu');
            $table->text('notiz')->nullable();
            $table->timestamp('empfangen_am')->useCurrent();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('status', 'idx_kontaktanfragen_status');
            $table->index('empfangen_am', 'idx_kontaktanfragen_empfangen_am');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webseite_kontaktanfragen');
    }
};
