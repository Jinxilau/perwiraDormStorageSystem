<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            // Administrator
            [
                'name'               => 'System Administrator',
                'email'              => 'admin@pdss.test',
                'email_verified_at'  => Carbon::now(),
                'password'           => Hash::make('password'),
                'role_id'            => 1, // administrator
                'matric_no'          => null,
                'gender'             => null,
                'phone_number'       => null,
                'year_of_study'      => null,
                'application_status' => 'approved',
                'remember_token'     => null,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ],

            // Staff
            [
                'name'               => 'Storage Staff',
                'email'              => 'staff@pdss.test',
                'email_verified_at'  => Carbon::now(),
                'password'           => Hash::make('password'),
                'role_id'            => 2, // staff
                'matric_no'          => null,
                'gender'             => null,
                'phone_number'       => null,
                'year_of_study'      => null,
                'application_status' => 'approved',
                'remember_token'     => null,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ],

            // Student
            [
                'name'               => 'Test Student',
                'email'              => 'student@pdss.test',
                'email_verified_at'  => Carbon::now(),
                'password'           => Hash::make('password'),
                'role_id'            => 3, // student
                'matric_no'          => 'A23CS9999',
                'gender'             => 'male',
                'phone_number'       => '0123456789',
                'year_of_study'      => 2,
                'application_status' => 'approved',
                'remember_token'     => null,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ],
        ]);
    }
}
