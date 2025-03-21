<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temperature_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temperature_record_id')->constrained()->onDelete('restrict');
            $table->foreignId('nurse_id')->constrained(table: 'users')->onDelete('restrict');
            $table->float('temperature');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temperature_details');
    }
};
