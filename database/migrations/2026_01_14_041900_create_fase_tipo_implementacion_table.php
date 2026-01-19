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
    
    Schema::create('fase_tipo_implementacion', function (Blueprint $table) {
        $table->id();

        // FK
        $table->foreignId('tipo_implementacion_id')
            ->constrained('tipo_implementacion')
            ->cascadeOnDelete();

        $table->foreignId('fase_id')
            ->constrained('fases')
            ->cascadeOnDelete();

        // Orden REAL (sin default)
      

        // Permite excluir fases sin borrarlas
        $table->boolean('activo')->default(true);

        $table->timestamps();

        // Evita duplicar fases en un mismo tipo
        $table->unique(['tipo_implementacion_id', 'fase_id']);
            });
        }

 


        
    public function down(): void
    {
        Schema::dropIfExists('fase_tipo_implementacion');
    }
};