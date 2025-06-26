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
        Schema::create('missing_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')->constrained('q_r_codes')->onDelete('cascade');

            $table->enum('status', ['reported', 'investigating', 'found', 'closed'])->default('reported');

            // Last see the item?
            $table->enum('last_seen_type', ['specific_date', 'dont_remember', 'other']);
            $table->date('last_seen_date')->nullable();
            $table->string('last_seen_other')->nullable();

            // When did you discover it was missing?
            $table->enum('discovered_missing_type', ['when_stored', 'specific_date', 'dont_remember', 'other']);
            $table->date('discovered_missing_date')->nullable();
            $table->string('discovered_missing_other')->nullable();
            
            $table->text('witnesses_and_info')->nullable();
            
            $table->timestamps();
            // $table->text('additional_information')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_reports');
    }
};
