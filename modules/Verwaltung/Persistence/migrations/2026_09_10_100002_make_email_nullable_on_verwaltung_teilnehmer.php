<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verwaltung_teilnehmer', function (Blueprint $table): void {
            // Laufkundschaft kommt ohne E-Mail-Adresse; der eindeutige Index
            // bleibt und lässt mehrere NULL-Werte zu.
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('verwaltung_teilnehmer', function (Blueprint $table): void {
            $table->string('email')->nullable(false)->change();
        });
    }
};
