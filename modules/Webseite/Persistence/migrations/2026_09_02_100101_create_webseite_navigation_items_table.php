<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('webseite_navigation_items', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('bezeichnung');
            $table->string('url');
            $table->boolean('extern')->default(false);
            $table->unsignedInteger('sortierung')->default(0);
            $table->boolean('aktiv')->default(true);
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->nullable();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);

            $table->index(['aktiv', 'sortierung'], 'idx_navigation_aktiv_sortierung');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webseite_navigation_items');
    }
};
