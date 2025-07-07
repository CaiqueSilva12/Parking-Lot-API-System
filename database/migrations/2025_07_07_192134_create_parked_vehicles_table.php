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
        Schema::create('parked_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_identifier');
            $table->timestamp('parked_at');
            $table->timestamp('unparked_at')->nullable();
            $table->integer('spots_occupied');
            $table->timestamps();
            $table->unique('vehicle_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parked_vehicles');
    }
};
