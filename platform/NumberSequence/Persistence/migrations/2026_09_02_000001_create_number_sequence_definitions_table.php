<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('nummernkreis_definitionen', function (Blueprint $table): void {
            $table->binary('id', 16)->primary();
            $table->string('code')->unique();
            $table->string('bezeichnung');
            $table->string('prefix');
            $table->unsignedTinyInteger('jahr_stellen')->default(4);
            $table->unsignedInteger('start_nummer')->default(1);
            $table->unsignedInteger('laenge_nummer')->default(5);
            $table->timestamp('angelegt_am')->useCurrent();
            $table->binary('angelegt_von', 16)->nullable();
            $table->timestamp('geaendert_am')->useCurrent();
            $table->binary('geaendert_von', 16)->nullable();
            $table->unsignedBigInteger('version')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nummernkreis_definitionen');
    }
};
