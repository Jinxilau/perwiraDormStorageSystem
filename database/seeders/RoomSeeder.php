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
        $now = Carbon::now();

        $semesters = [
            ['2024/2025', 1, '2024-10-01', '2025-02-15'],
            ['2024/2025', 2, '2025-03-01', '2025-07-15'],
            ['2025/2026', 1, '2025-10-01', '2026-02-15'],
            ['2025/2026', 2, '2026-03-01', '2026-07-15'],
        ];

        $data = [];

        foreach ($semesters as $sem) {
            $start = Carbon::parse($sem[2]);
            $end   = Carbon::parse($sem[3]);

            // Logic: If current, set to 1. If not, set to NULL.
            $isCurrent = $now->between($start, $end) ? 1 : null;

            $data[] = [
                'academic_year' => $sem[0],
                'semester_no'   => $sem[1],
                'start_date'    => $sem[2],
                'end_date'      => $sem[3],
                'is_current'    => $isCurrent, // 1 or NULL
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // Upsert remains the same
        DB::table('semesters')->upsert(
            $data,
            ['academic_year', 'semester_no'],
            ['start_date', 'end_date', 'is_current', 'updated_at']
        );
    }
}
