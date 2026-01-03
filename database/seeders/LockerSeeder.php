<?php

namespace Database\Seeders;

use App\Models\StorageRoom;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = StorageRoom::pluck('id');

        $batchData = [];

        foreach ($rooms as $roomId) {
            for ($i = 1; $i <= 30; $i++) {
                $batchData[] = [
                    'storage_room_id' => $roomId,
                    'code'            => 'L' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'size'            => '45x90x55',
                    'status'          => 'available',
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ];
            }
        }

        // 3. IDEMPOTENT SAVING (CI/CD Safe)
        // This will INSERT if new, or UPDATE if the room+code exists.
        // It prevents "Duplicate Entry" errors.
        DB::table('lockers')->upsert(
            $batchData,
            ['storage_room_id', 'code'], // The Unique Constraint Keys
            ['size', 'status', 'updated_at'] // Columns to update if found
        );
    }
}
