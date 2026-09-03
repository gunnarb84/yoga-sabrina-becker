<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_teilnehmer', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('email');
            $table->string('vorname');
            $table->string('nachname');
            $table->string('adresszeile_1')->nullable();
            $table->string('adresszeile_2')->nullable();
            $table->string('postleitzahl')->nullable();
            $table->string('stadt')->nullable();
            $table->string('telefon')->nullable();
            $table->date('geburtsdatum')->nullable();
            $table->text('gesundheitsinformationen')->nullable();
            $table->boolean('gesundheitsinformationen_einwilligung')->default(false);
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->unique('email', 'idx_teilnehmer_email_unique');
            $table->index(['nachname', 'vorname'], 'idx_teilnehmer_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_teilnehmer');
    }
};
