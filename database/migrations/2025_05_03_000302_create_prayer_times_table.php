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
        Schema::create('prayer_times', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('city_id');
            $table->timestamp('date')->nullable();
            $table->string('imsak')->nullable();
            $table->string('subuh')->nullable();
            $table->string('terbit')->nullable();
            $table->string('dhuha')->nullable();
            $table->string('dzuhur')->nullable();
            $table->string('ashar')->nullable();
            $table->string('maghrib')->nullable();
            $table->string('isya')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_times');
    }
};
