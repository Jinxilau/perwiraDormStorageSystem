<?php

namespace App\Exports;

use App\Models\StorageApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StorageReportExport implements FromCollection, WithHeadings, WithMapping
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
}
