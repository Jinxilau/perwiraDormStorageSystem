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
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('role_id')->constrained('user_roles')->onDelete('restrict')->after('id')->default(3); // TODO
            // $table->foreignId('semester_id')->nullable()->after('role_id')->constrained('semesters')->nullOnDelete(); // Current semester of the user, optional for admin/staff

            $table->string('matric_no')->unique()->after('role_id')->nullable(); // Student matriculation number, sometime optional for admin/staff
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('matric_no');
            $table->string('phone_number')->unique()->nullable()->after('gender');
            $table->tinyInteger('year_of_study')->unsigned()->nullable()->after('phone_number');
            $table->enum('application_status', ['pending', 'approved', 'rejected'])->default('pending')->after('year_of_study'); // Student account application status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['account_status', 'application_status', 'phone_number', 'gender', 'year_of_study', 'semester']);
        });
    }
};
