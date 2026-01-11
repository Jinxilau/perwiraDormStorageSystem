<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('open_area_id')->nullable()->constrained('open_areas')->onDelete('set null');
            $table->foreignId('locker_id')->nullable()->constrained('lockers')->onDelete('set null');

            $table->enum('storage_application_status', ['pending', 'approved', 'rejected']);
            $table->text('note')->nullable(); # Admin's note
            // $table->date('start_date'); // These not used anymore, because can be replaced using operational period in the storagerooms table
            // $table->date('end_date');
            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE storage_applications ADD CONSTRAINT check_storage_type 
        CHECK ((locker_id IS NOT NULL AND open_area_id IS NULL) 
        OR (locker_id IS NULL AND open_area_id IS NOT NULL))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_applications');
    }
};
