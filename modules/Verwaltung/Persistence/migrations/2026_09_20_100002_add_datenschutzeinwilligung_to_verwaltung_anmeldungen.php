<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('verwaltung_anmeldungen', function (Blueprint $table): void {
            $table->boolean('datenschutz_einwilligung')->default(false);
            $table->timestamp('datenschutz_einwilligung_am')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('verwaltung_anmeldungen', function (Blueprint $table): void {
            $table->dropColumn(['datenschutz_einwilligung', 'datenschutz_einwilligung_am']);
        });
    }
};
