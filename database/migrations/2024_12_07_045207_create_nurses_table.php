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
        Schema::create('nurses', function (Blueprint $table) {
            $table->id(); // ID automático
            $table->string('speciality'); // Especialidad de la enfermera
            $table->string('area'); // Área asignada
            $table->boolean('active')->default(true); // Estado activo/inactivo
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
