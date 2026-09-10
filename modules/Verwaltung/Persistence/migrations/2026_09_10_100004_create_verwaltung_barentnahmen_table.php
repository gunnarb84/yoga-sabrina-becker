<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_barentnahmen', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->date('datum');
            $table->decimal('betrag', 19, 4);
            $table->string('zweck');
            $table->string('fremdbelegnummer')->nullable();
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index('datum', 'idx_barentnahmen_datum');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_barentnahmen');
    }
};
