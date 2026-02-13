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
        Schema::create('estimaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_implementacion_id')->constrained('tipo_implementacion')->onDelete('cascade');
            $table->string('nombre_tipo_implementacion')->nullable();
            $table->foreignId('complejidad_id')->constrained('nivel_complejidads')->onDelete('cascade');
            $table->integer('total_minutos');
            $table->decimal('total_horas', 8, 2);
            $table->text('comentarios')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                ->default('pendiente')
                ->after('id');

            // Datos adicionales que agregaste
            $table->string('nombre_empresa');
            $table->string('responsable')->nullable();
            $table->string('id_negocio')->nullable();
            $table->string('bitrix_group_id')->nullable();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimaciones');
    }
};