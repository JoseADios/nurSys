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
        Schema::create('medical_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained()->onDelete('restrict');
            $table->time('start_date');
            $table->string('order');
            $table->string('regime')->nullable();
            $table->dateTime('suspended_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_order_details');
    }
};
