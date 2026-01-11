<?php

namespace App\Exports;

use App\Models\StorageApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StorageReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function collection()
    {
        return StorageApplication::query()
            ->leftJoin('semesters', 'storage_applications.semester_id', '=', 'semesters.id')
            ->leftJoin('users as applicants', 'storage_applications.applicant_id', '=', 'applicants.id')
            ->leftJoin('lockers', 'storage_applications.locker_id', '=', 'lockers.id')
            ->leftJoin('open_areas', 'storage_applications.open_area_id', '=', 'open_areas.id')
            ->leftJoin('storage_rooms', function ($join) {
                $join->on('lockers.storage_room_id', '=', 'storage_rooms.id')
                    ->orOn('open_areas.storage_room_id', '=', 'storage_rooms.id');
            })
            ->select(
                'storage_applications.id',
                'semesters.academic_year',
                'semesters.semester_no',
                'applicants.name as applicant_name',
                'storage_applications.storage_application_status',
                'lockers.code as locker_code',
                'storage_rooms.room_name',
                'storage_applications.created_at'
            )
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Application ID',
            'Academic Year',
            'Semester',
            'Applicant Name',
            'Storage Type',
            'Locker / Area',
            'Storage Room',
            'Application Status',
            'Applied At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->academic_year ?? '-',
            $row->semester_no ? 'Semester ' . $row->semester_no : '-',
            $row->applicant_name,
            $row->locker_code ? 'Locker' : 'Open Area',
            $row->locker_code ?? 'Open Area',
            $row->room_name ?? '-',
            ucfirst($row->storage_application_status),
            $row->created_at->format('Y-m-d'),
        ];
    }

    public function styles($sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'], // Blue background
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Add borders to all cells
            'A1:I' . ($this->collection()->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Application ID
            'B' => 18,  // Academic Year
            'C' => 15,  // Semester
            'D' => 25,  // Applicant Name
            'E' => 15,  // Storage Type
            'F' => 18,  // Locker / Area
            'G' => 20,  // Storage Room
            'H' => 20,  // Application Status
            'I' => 15,  // Applied At
        ];
    }

    public function title(): string
    {
        return 'Perwira Dorm Storage Report';
    }
}
