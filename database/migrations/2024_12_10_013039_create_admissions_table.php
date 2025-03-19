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
            $table->foreignId('bed_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('patient_id')->constrained()->onDelete('restrict');
            $table->foreignId('receptionist_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('restrict');
            $table->string('admission_dx');
            $table->string('doctor_sign')->nullable();
            $table->string('final_dx')->nullable();
            $table->text('comment')->nullable();
            $table->datetime('discharged_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
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
