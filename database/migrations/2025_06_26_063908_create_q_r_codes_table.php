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
        Schema::create('q_r_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_application_id')->constrained('storage_applications')->onDelete('cascade')->after('id');

            $table->text('qr_token');
            $table->enum('status', [
                'not_scanned',
                'checked_in',
                'checked_out',
            ])->default('not_scanned');
            $table->unsignedInteger('scanned_count')->default(0);

            $table->string('checkin_item_photo')->nullable(); # Photo of where the item is stored within the storage unit
            $table->string('storage_zone')->nullable(); # Zone where the item is stored within the storage unit

            $table->timestamp('qr_expires_at')->nullable(); // 10 days after the start of the semester booted() function in Semester model

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('q_r_codes');
    }
};
