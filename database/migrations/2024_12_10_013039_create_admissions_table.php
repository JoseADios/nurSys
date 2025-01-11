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
            $table->unsignedBigInteger('bed_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('recepcionist_id');
            $table->unsignedBigInteger('doctor_id');
            $table->foreign('bed_id')->references('id')->on('beds')->restricOnDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->restricOnDelete('cascade');
            $table->foreign('recepcionist_id')->references('id')->on('users')->restricOnDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->restricOnDelete('cascade');
            $table->string('admission_dx');
            $table->string('final_dx')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('in_process')->default(true);
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
