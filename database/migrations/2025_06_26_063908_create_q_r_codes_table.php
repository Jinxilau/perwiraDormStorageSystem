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
            
            $table->text('qr_token');
            $table->enum('status', ['not_scanned', 'checked_in', 'checked_out', 'expired'])->default('not_scanned');
            $table->unsignedInteger('scanned_count')->default(0);
            
            $table->timestamp('qr_expires_at')->nullable();
            $table->timestamps();
            
            $table->foreignId('storage_application_id')->constrained('storage_applications')->onDelete('cascade');
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
