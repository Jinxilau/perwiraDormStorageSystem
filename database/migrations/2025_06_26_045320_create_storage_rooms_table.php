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
        Schema::create('storage_rooms', function (Blueprint $table) {
            $table->id();

            $table->string('room_name', 100);
            $table->enum('room_status', ['open', 'closed', 'under_maintenance'])->default('open');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_rooms');
    }
};
