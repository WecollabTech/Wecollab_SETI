<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estimacion_integraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimacion_id')
                ->constrained('estimaciones')
                ->cascadeOnDelete();

            $table->foreignId('integracion_id')
                ->constrained('integraciones')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimacion_integraciones');
    }
};