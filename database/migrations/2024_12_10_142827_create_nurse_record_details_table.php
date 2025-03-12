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
        Schema::create('nurse_record_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurse_record_id')
                ->constrained()->onDelete('restrict');
            $table->string('medication');
            $table->text('comment')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_record_details');
    }
};
