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
        Schema::create('stored_items', function (Blueprint $table) {
            $table->id();

            $table->string('item_name', 100);
            $table->enum('estimated_size', ['small', 'medium', 'large']);
            $table->enum('item_type', ['electronic', 'furniture', 'container', 'luggage', 'clothing', 'home_appliance', 'other'])->default('other');
            $table->text('item_description')->nullable();
            $table->string('item_photo_path')->nullable();

            $table->timestamps();

            $table->foreignId('storage_application_id')->constrained('storage_applications')->onDelete('cascade');
            $table->foreignId('missing_report_id')->nullable()->constrained('missing_reports')->onDelete('set null'); // TODO
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stored_items');
    }
};
