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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); //The admin/staff who performed the action (Nullable incase the systme perform some action)
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->morphs('subject'); // The model that the action was performed on

            $table->string('action'); // A short key describing the action
            $table->text('description')->nullable(); // Human readable text
            $table->ipAddress('ip_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
