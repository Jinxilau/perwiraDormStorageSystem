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
        Schema::create('lockers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_room_id')->constrained('storage_rooms')->onDelete('cascade');

            $table->string('code', 50);
            $table->string('size', 50);
            $table->enum('status', [
                'available', // can be assigned
                'reserved', // assigned but not yet occupied/checked in
                'occupied', // currently in use/checked in
                'under_maintenance' // temporarily unavailable
            ])->default('available');

            $table->timestamps();

            $table->unique(['storage_room_id', 'code']); // Unique code per room
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lockers');
    }
};
