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
        Schema::create('medication_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_record_detail_id')
                ->constrained()->onDelete('restrict');
            $table->foreignId('nurse_id')->nullable();
                ->constrained(table:'users')->onDelete('restrict')->nullable();
            $table->boolean('applied')->default(false);
            $table->dateTime('scheduled_time');
            $table->dateTime('administered_time')->nullable();
            $table->string('nurse_sign')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_notifications');
    }
};
