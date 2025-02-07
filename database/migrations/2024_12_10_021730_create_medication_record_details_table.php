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
            $table->string('dose');
            $table->string('route');
            $table->integer('fc');
            $table->time('start_time');
            $table->integer('interval_in_hours');
            $table->boolean('active')->default(true);
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
