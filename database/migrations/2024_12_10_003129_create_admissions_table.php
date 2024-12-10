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
            $table->integer('bed_id');
            $table->integer('patient_id');
            $table->integer('recepcionist_id');
            $table->integer('doctor_id');
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

