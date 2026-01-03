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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year');
            $table->integer('semester_no');
            $table->date('start_date');
            $table->date('end_date');

            // 1. Make it nullable
            $table->boolean('is_current')->nullable()->default(null);

            $table->timestamps();

            // 2. Add composite unique for upsert safety
            $table->unique(['academic_year', 'semester_no']);

            // 3. The MySQL Trick: Standard Unique Index
            // MySQL allows multiple NULLs, but only ONE '1'.
            $table->unique('is_current');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
