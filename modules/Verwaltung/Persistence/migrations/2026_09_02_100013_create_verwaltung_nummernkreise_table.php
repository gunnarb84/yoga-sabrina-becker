<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('verwaltung_nummernkreise', function (Blueprint $table): void {
            $table->string('code');
            $table->unsignedInteger('jahr');
            $table->unsignedInteger('letzte_nummer')->default(0);
            $table->timestamp('angelegt_am')->useCurrent();
            $table->timestamp('geaendert_am')->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->primary(['code', 'jahr']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verwaltung_nummernkreise');
    }
};
