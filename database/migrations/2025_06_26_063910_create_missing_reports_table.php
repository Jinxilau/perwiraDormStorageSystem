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
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();

            $table->enum('status', [
                'pending', // Student submitted, Admin hasn't looked yet
                'investigating', // Admin is checking logs/CCTV
                'found', // Item located
                'lost', // Item not found. Case Closed.
                'dismissed' // Report invalid or false alarm
            ])->default('pending');

            // Last see the item?
            $table->enum('last_seen_type', ['specific_date', 'dont_remember', 'range']);
            $table->date('last_seen_date')->nullable();
            $table->enum('last_seen_range', ['today', 'yesterday', 'this_week', 'last_week', 'last_month', 'longer'])->nullable();

            $table->text('last_seen_location');

            // When did you discover it was missing?
            $table->enum('discovered_missing_type', ['specific_date', 'dont_remember', 'range']);
            $table->date('discovered_missing_date')->nullable();
            $table->enum('discovered_missing_range', ['today', 'yesterday', 'this_week', 'last_week', 'last_month', 'longer'])->nullable();

            $table->text('witnesses_and_info')->nullable();

            $table->text('admin_note')->nullable();

            $table->timestamps();
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
