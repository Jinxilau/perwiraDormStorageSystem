<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('storage_rooms')->insert([
            [
                'id' => 1,
                'room_name' => 'E1-03',
                'room_status' => 'open',
            ],
            [
                'id' => 2,
                'room_name' => 'E1-06',
                'room_status' => 'open',
            ],
        ]);
    }
}
