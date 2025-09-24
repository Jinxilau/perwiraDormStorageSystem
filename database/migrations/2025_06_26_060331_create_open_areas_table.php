<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration // TODO
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('open_areas', function (Blueprint $table) {
            $table->id();
            
            $table->enum('area_status', ['available', 'occupied', 'under_maintenance'])->default('available');
            // $table->decimal('estimated_storage_capacity', 10, 2); 
            // $table->decimal('occupied_storage_capacity', 10, 2)->default(0);
            
            $table->timestamps();

            $table->foreignId('storage_room_id')->constrained('storage_rooms')->onDelete('cascade');
            // $table->foreignId('application_id')->constrained('storage_applications')->nullable(); //TODO
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_areas');
    }
};
