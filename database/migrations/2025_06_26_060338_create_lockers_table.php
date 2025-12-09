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
            
            $table->string('size', 50); 
            $table->enum('status', ['available', 'reserved', 'occupied', 'under_maintenance'])->default('available');
            
            $table->timestamps();
            
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
