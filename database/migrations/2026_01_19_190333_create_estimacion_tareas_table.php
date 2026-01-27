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
        Schema::create('estimacion_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimacion_fase_id')->constrained('estimacion_fases')->onDelete('cascade');
            $table->string('nombre_tarea');
            $table->integer('duracion_minuto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimacion_tareas');
    }
};