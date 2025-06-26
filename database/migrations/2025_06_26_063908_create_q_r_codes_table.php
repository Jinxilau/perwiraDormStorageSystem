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
            $table->foreignId('storage_application_id')->constrained('storage_applications')->onDelete('cascade');

            $table->text('qr_content');
            $table->enum('qr_status', ['active', 'expired', 'disabled', 'scanned'])->default('active');
            $table->unsignedInteger('scanned_count')->default(0);
            $table->string('qrcode_url')->nullable();
            
            $table->timestamp('qr_expires_at')->nullable();
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
