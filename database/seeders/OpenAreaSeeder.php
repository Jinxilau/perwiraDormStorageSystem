<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpenAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Fetch all existing Storage Rooms dynamically
        // This makes your seeder scalable: Add a new room? This seeder handles it.
        $rooms = DB::table('storage_rooms')->pluck('id');

        foreach ($rooms as $roomId) {

            // 2. Treat the Open Area as a "Logical Zone"
            // We use updateOrInsert to ensure we have ONE "Main Open Area" per room.
            // Even if run this 10 times, you won't get duplicate areas.
            DB::table('open_areas')->updateOrInsert(
                [
                    // The "Composite Key" to identify this specific zone
                    // Currently, it's just the room ID (1 zone per room).
                    // FUTURE: You will add 'name' => 'General Zone' here.
                    'storage_room_id' => $roomId,
                ],
                [
                    // The attributes to set/update
                    'area_status' => 'available',
                    'created_at'  => Carbon::now(), // Only used on Insert
                    'updated_at'  => Carbon::now(),
                ]
            );
        }
    }
}
