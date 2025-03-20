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
        Schema::create('elimination_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temperature_record_id')->constrained()->onDelete('restrict');
            $table->foreignId('nurse_id')->constrained('users')->onDelete('restrict');
            $table->integer('evacuations');
            $table->string('urinations', 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elimination_records');
    }
};
