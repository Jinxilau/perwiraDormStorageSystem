<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define the data array
        $roles = [
            [
                'id' => 1, // <--- Hard-coded ID
                'role_name' => 'administrator',
                'role_description' => 'Has full access to system management, including user approvals, storage monitoring, and reporting.',
                'role_scope' => json_encode([
                    'user_management',
                    'storage_approval',
                    'reporting',
                    'system_settings'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2, // <--- Hard-coded ID
                'role_name' => 'staff',
                'role_description' => 'Responsible for day-to-day operations including reviewing storage applications, managing check-ins/outs, and tracking inventory.',
                'role_scope' => json_encode([
                    'application_review',
                    'check_in_out',
                    'inventory_tracking'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3, // <--- Hard-coded ID
                'role_name' => 'student',
                'role_description' => 'Can apply for storage, view application status, and check in/out approved items.',
                'role_scope' => json_encode([
                    'storage_application',
                    'view_status',
                    'qr_check_in_out'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // 2. Run the Upsert
        // Arg 1: The data
        // Arg 2: The column to check for uniqueness ('id')
        // Arg 3: The columns to update if the ID already exists
        DB::table('user_roles')->upsert(
            $roles,
            ['id'],
            ['role_name', 'role_description', 'role_scope', 'updated_at']
        );
    }
}
