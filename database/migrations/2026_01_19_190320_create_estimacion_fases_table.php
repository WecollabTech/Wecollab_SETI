<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estimacion_fases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimacion_id')->constrained('estimaciones')->onDelete('cascade');
            $table->foreignId('fase_id')->constrained('fases')->onDelete('cascade');
            $table->integer('minutos');
            $table->decimal('horas', 8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimacion_fases');
    }
};