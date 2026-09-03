<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verwaltung_anmeldungen', function (Blueprint $table): void {
            $table->string('zahlungsstatus')->default('offen')->after('zahlungsart');

            $table->index('zahlungsstatus', 'idx_anmeldungen_zahlungsstatus');
        });
    }

    public function down(): void
    {
        Schema::table('verwaltung_anmeldungen', function (Blueprint $table): void {
            $table->dropIndex('idx_anmeldungen_zahlungsstatus');
            $table->dropColumn('zahlungsstatus');
        });
    }
};
