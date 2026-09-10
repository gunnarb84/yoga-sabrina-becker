<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verwaltung_anmeldungen', function (Blueprint $table): void {
            // Bestandsanmeldungen stammen aus dem Online-Anmeldeprozess.
            $table->string('herkunft')->default('webseite')->after('zahlungsart');
        });
    }

    public function down(): void
    {
        Schema::table('verwaltung_anmeldungen', function (Blueprint $table): void {
            $table->dropColumn('herkunft');
        });
    }
};
