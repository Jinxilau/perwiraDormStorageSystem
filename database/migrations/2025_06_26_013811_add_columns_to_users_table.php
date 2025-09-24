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

            $table->string('matric_no')->unique()->after('role_id');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('account_status', ['active', 'inactive'])->default('inactive'); # used for soft delete
            $table->enum('application_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('phone_number')->unique()->nullable();
            $table->tinyInteger('year_of_study')->unsigned()->nullable();
            $table->tinyInteger('semester')->unsigned()->nullable();
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
