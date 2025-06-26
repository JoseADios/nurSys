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
        Schema::create('medication_record_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_record_id')->constrained()->onDelete('restrict');
            $table->foreignId('medical_order_detail_id')->constrained()->onDelete('restrict');
            $table->string('drug');
            $table->integer('dose');
            $table->string('dose_metric');
            $table->string('route');
            $table->integer('fc');
            $table->dateTime('start_time');
            $table->integer('interval_in_hours')->nullable();
            $table->integer('nebulization_time')->nullable();
            $table->boolean('nebulized')->default(false);
            $table->boolean('active')->default(true);
          $table->timestamp('suspended_at')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_record_details');
    }
};
