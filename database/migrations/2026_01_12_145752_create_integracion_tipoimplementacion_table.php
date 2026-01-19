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
        Schema::create('integracion_tipoimplementacion', function (Blueprint $table) {
            $table->id();

                $table->foreignId('tipo_implementacion_id')
                  ->constrained('tipo_implementacion')
                  ->onDelete('cascade');

           
            $table->foreignId('integracion_id')->constrained('integraciones')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracion_tipoimplementacion');
    }
};