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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->integer('id_bed');
            $table->integer('id_patient');
            $table->integer('id_recepcionist');
            $table->integer('id_doctor');
            $table->string('admission_dx');
            $table->string('final_dx')->nullable();
            $table->datetime('created_at');
            $table->string('comment')->nullable();
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};

