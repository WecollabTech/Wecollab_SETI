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
        Schema::create('estimacion_integracion_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimacion_integracion_id')
                ->constrained('estimacion_integraciones')
                ->cascadeOnDelete();

            $table->string('nombre_tarea_integracion');
            $table->integer('duracion_estimada_minutos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimacion_integracion_tareas');
    }
};