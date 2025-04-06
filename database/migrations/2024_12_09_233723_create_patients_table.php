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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('first_surname');
            $table->string('second_surname')->nullable();
            $table->string('phone');
            $table->string('identification_card')->nullable();
            $table->string('nationality');
            $table->string('email')->nullable();
            $table->date('birthdate');
            $table->string('position')->nullable();
            $table->string('marital_status');
            $table->string('address');
            $table->string('ars')->nullable();
            $table->string('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
