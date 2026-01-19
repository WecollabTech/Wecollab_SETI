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
        Schema::create('nivel_complejidads', function (Blueprint $table) {
            $table->id();
                $table->string('nombre', 50);
                $table->decimal('factor', 5, 2);
                $table->text('descripcion')->nullable();
                $table->boolean('estado')->default(true);
                $table->text('rubricas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nivel_complejidads');
    }
};