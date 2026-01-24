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
        Schema::create('integracion_tareas', function (Blueprint $table) {
            $table->id();
            // Relación con Integraciones
            $table->foreignId('integracion_id')
                ->constrained('integraciones')
                ->onDelete('cascade');

            // Campos principales del formulario
            $table->string('titulo');
            $table->string('id_proceso')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('duracion_minuto')->nullable();
            $table->integer('orden')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracion_tareas');
    }
};