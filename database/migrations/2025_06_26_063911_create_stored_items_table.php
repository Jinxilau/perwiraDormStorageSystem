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
            $table->foreignId('storage_application_id')->constrained('storage_applications')->onDelete('cascade');
            $table->foreignId('missing_report_id')->nullable()->constrained('missing_reports')->onDelete('set null'); // TODO

            $table->string('item_name', 100);
            $table->enum('estimated_size', ['small', 'medium', 'large']);
            $table->enum('item_type', ['electronic', 'furniture', 'container', 'luggage', 'clothing', 'home_appliance', 'other'])->default('other');
            
            $table->enum('item_condition', [
                'fragile', // Items that require special handling. Useful for staff to sort and place safely.
                'bulky', // Takes larger volume or unusual shape Affects storage space planning
                'boxed', // Easiest to stack and store. Fit well into shelves Good for efficient space use
                'misc' // Everything else. Prevents overcomplicating classification
            ])->default('misc');
            
            $table->text('item_description')->nullable();
            $table->string('item_photo_path')->nullable(); # Photo of the item itself

            $table->timestamps();
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
