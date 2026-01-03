<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class, # User Roles Seeder
            UserSeeder::class, # Default Admin User Seeder
            RoomSeeder::class, # Storage Rooms Seeder
            SemesterSeeder::class, # Academic Semesters Seeder
            SettingSeeder::class, # Application Settings Seeder
            OpenAreaSeeder::class, # Open Areas Seeder
            LockerSeeder::class, # Lockers Seeder
        ]);
    }
}
